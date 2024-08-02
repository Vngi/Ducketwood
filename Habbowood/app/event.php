<?php

function handleEventRequests()
{
    // SQLite database file path
    $db_path = __DIR__ . '/../private/habbowood.db';

    // Database connection setup (using SQLite)
    $pdo = new PDO('sqlite:' . $db_path);

    // Set PDO to throw exceptions on errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // IP rate limiting configuration
    $ip = $_SERVER['REMOTE_ADDR']; // Get user's IP address
    $limitPeriod = 3600; 
    $maxVotesPerPeriod = 3; // Maximum votes allowed per IP address within the period

    // Handle actions
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
        $action = $_GET['action'];

        if ($action === "getTopMovies" && isset($_GET['language']) && $_GET['language'] === "uk") {
            $query_topmovie = "SELECT * FROM movies ORDER BY votes DESC LIMIT 20";
            $stmt = $pdo->query($query_topmovie);
            $stmt->execute();
            $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $xmlResponse = '<?xml version="1.0" encoding="UTF-8" ?><d>';
            foreach ($movies as $movie) {
                $director = htmlspecialchars_decode($movie['director'], ENT_QUOTES);
                $title = htmlspecialchars_decode($movie['title'], ENT_QUOTES);
                $xmlResponse .= '<i><![CDATA[' . $director . ' (' . $title . ')]]></i>';
            }
            $xmlResponse .= '</d>';
            echo $xmlResponse;
        } elseif ($action === "getMovie" && isset($_GET['title'])) {
            $title = $_GET['title'];
            $query_getmovie = "SELECT * FROM movies WHERE LOWER(title) = LOWER(:title)";
            $stmt = $pdo->prepare($query_getmovie);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->execute();
            $movie = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($movie) {
                $xml = $movie['xml'];
                echo $xml;
            } else {
                echo '<?xml version="1.0" encoding="UTF-8" ?><d><alert_movienotfound><![CDATA[No match was found, please check the spelling]]></alert_movienotfound></d>';
            }
        } elseif ($action === "voteMovie" && isset($_GET['title']) && isset($_GET['voteType'])) {
            // Implement IP rate limiting
            $query_check_ip = "SELECT COUNT(*) as count FROM votes WHERE ip_address = :ip AND created_at >= datetime('now', '-$limitPeriod seconds')";
            $stmt = $pdo->prepare($query_check_ip);
            $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] >= $maxVotesPerPeriod) {
                echo 'You have reached the maximum votes allowed in a period. Please try again later.';
                exit;
            }

            $title = $_GET['title'];
            $voteType = $_GET['voteType'];

            // Determine vote operation based on voteType
            if ($voteType === '1') {
                $voteOperation = '+1';
            } elseif ($voteType === '0') {
                $voteOperation = '-1';
            } else {
                echo 'Invalid voteType.';
                exit;
            }

            // Update votes in the database
            $updateQuery = "UPDATE movies SET votes = votes {$voteOperation} WHERE title = :title";
            $stmt = $pdo->prepare($updateQuery);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            if ($stmt->execute()) {
                // Record vote with IP address and timestamp
                $insertVoteQuery = "INSERT INTO votes (movie_id, ip_address, created_at) VALUES ((SELECT id FROM movies WHERE title = :title), :ip, datetime('now'))";
                $stmt = $pdo->prepare($insertVoteQuery);
                $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
                $stmt->execute();

                // Fetch updated votes count
                $query_fetchvotes = "SELECT votes FROM movies WHERE title = :title";
                $stmt = $pdo->prepare($query_fetchvotes);
                $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                $stmt->execute();
                $currentVotes = $stmt->fetchColumn();

                if ($currentVotes !== false) {
                    echo '<?xml version="1.0" encoding="UTF-8" ?><d><votes>' . $currentVotes . '</votes></d>';
                } else {
                    echo '<?xml version="1.0" encoding="UTF-8" ?><d>No movie found with title: ' . $title . '</d>';
                }
            } else {
                echo 'Failed to update vote.';
            }
        } else {
            echo 'Unknown or invalid action.';
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'saveMovie') {
            $newtitle = $_POST['title'];
            $director = $_POST['director'];
            $xml = $_POST['xml'];
            $email = $_POST['email'];

            // Check if the movie title already exists
            $checkQuery = "SELECT * FROM movies WHERE title = :newtitle";
            $stmt = $pdo->prepare($checkQuery);
            $stmt->bindParam(':newtitle', $newtitle, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                echo '<?xml version="1.0" encoding="UTF-8" ?>&res=exists';
            } else {
                // Insert new movie
                $insertQuery = "INSERT INTO movies (director, title, xml, email, votes) VALUES (:director, :newtitle, :xml, :email, 0)";
                $stmt = $pdo->prepare($insertQuery);
                $stmt->bindParam(':director', $director, PDO::PARAM_STR);
                $stmt->bindParam(':newtitle', $newtitle, PDO::PARAM_STR);
                $stmt->bindParam(':xml', $xml, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                if ($stmt->execute()) {
                    echo '<?xml version="1.0" encoding="UTF-8" ?>&res=ok';
                } else {
                    echo '<?xml version="1.0" encoding="UTF-8" ?>&res=error';
                }
            }
        } elseif ($action === 'voteMovie') {
            // Implement IP rate limiting for POST requests (if applicable)
            echo 'Invalid request method for action: voteMovie.';
        } else {
            echo 'Unknown or invalid action.';
        }
    } else {
        echo 'Unknown or invalid request.';
    }

    $pdo = null;
}

// Call the facade function to handle the event requests
handleEventRequests();
?>

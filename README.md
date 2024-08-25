# Habbowood

Habbowood is an updated version of an old Habbo feature. This repository includes a reworked database system and an updated `event.php` that supports the leaderboard functionality. Here’s what you need to know:

MOST IMPORTANT! PLEASE TEST IT PRIVATELY TO MAKE SURE THE DATABASE CANNOT BE ACCESSED BEFORE RISKING ANY INFORMATION BEING EXPOSED, ASWELL AS THE EVENT.PHP AS IT COULD LEAD TO EXPLOITS IN THE FUTURE!

## Features

- **Database**: The system uses SQLite. Note that the database contains IP addresses to prevent spam voting (limit of 2 votes per person per hour). Ensure to add an `.htaccess` file or an alternative method to secure this file from direct access.
  
- **Email Check Removal**: The requirement for email addresses has been removed. Instead, users can now enter their Twitter handle when submitting their movie.

- **SWF Images**: Custom elements have been included, but you can replace them with the original SWF images if you prefer.

- **Text Customization**: You can edit the text related to ducket in `UK/Texts.xml`.

- **Credits**: If you use this code on your Habbo fansite, please give credit to Ducket.net or myself.

- **Customizable**: You have full rights to modify and use the code as needed, but remember it is based on Habbo's functionality.

## Installation

1. **Database File**: The database is located at `private/habbowood.db`. The `.htaccess` file is included, but you may need to adjust it if you're not on Windows.

2. **Ruffle**: This repository includes Ruffle for Shockwave emulation.

3. **SWF and Text Adjustments**: You can modify SWF images and text using JPEXS Free Flash Decompiler.

## Updates

- **Events.php**: Completely rewritten to use an updated version of SQL (SQLite).
- **Main.swf**: Modified to remove the email address requirement when submitting movies.

## Conclusion

- If you want to simply have a working function without any SWF edits including the removal of email checks, you can simply replace your event.php with the one provided here as well using our database. 
- Myself or Ducket are not responsible for how you use this code, if you've not set it up correctly that's on you I will no longer keep this updated unless I plan on updating it on our side too!

For more information on using JPEXS Free Flash Decompiler, visit [JPEXS Free Flash Decompiler]([https://www.free-decompiler.com/flash/](https://github.com/jindrapetrik/jpexs-decompiler). 

Feel free to explore and customize this project to fit your needs!

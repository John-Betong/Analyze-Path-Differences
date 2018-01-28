# File Integrity Check

### Introduction:
Have you ever wondered what files have been newly created, last modified or even deleted?
Look no further - this is the App' for that :)

File Integrity Class
A simple Class that interrogates all files in a site path.
File contents are hashed and a "snapshot" saved to a dynamically created database table.
Unfortunately folder names must be "fixed" to in order to conform :)
Any time afterwards the "snapshot" can be compared against the current files.
All discrepancies are shown which facilitates finding problematic files.
A new "snapshot" can be created once all discrepances have been resolved.

### Results of Interrogation:
    Amended files
    Deleted files
    New files

### Installation:
    1. Download and extract any of the following zip files into a new LOCALHOST "folder-name":
    2. source.zip source.tar.xz source.7z
    3. Use PhpMyAdmin to create a "DB_HOST" Database named "DB_NAME"
    4. Set Database permissions for:
        "DB_USER"
        "DB_PWD"
    5. Edit:   _config.php
        Set Database values for: DB_HOST, DB_USER, DB_PWD, DB_NAME
        More paths may be added to the "folder-name" path and its parent
        Browse to "folder-name"/index.php
        Test thoroughly and once satisfied upload to your online site.

#### [Online Demo](https://johns-jokes.com/downloads/sp-a/detect-file-changes/ver-002/)

   
[Screenshot](https://johns-jokes.com/downloads/sp-a/detect-file-changes/ver-002/imgs/screenshot-2018-01-28-31.3kb.png "ScreenDump")


 

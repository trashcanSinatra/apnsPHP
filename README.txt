--- Database Setup ---

SQL script to create the MySQL database and table is in _assets/_sql

From there you can just change the host, username, and password attributes 
in the dataLink class to match accordingly.

--- Database Setup End ---


--- Project Setup ---

Place all files and directories into the servers
root folder inside a directory named "ios_push".


Place your apple developer certificate in _assets/_certs/
Set SSL password as the $apnsPass variable in push.class.php


Web Service # 1  Token Registration

direct request to:

www.<host>/ios_push/registerToken.php?id=deviceToken


This service stores the device token in the "id" get request
to the database.  


Web App # 1  Apple Push Notification Message Mananger

www.<host>/ios_push/messages.php


The messages.php page gives you the ability to store messages 
in the database for later, store a message as "saved" and 
immediately push, or flush all stored messages.


-- Project Setup End --


-- Debug Options  --

Change the debug paramter in apns.php to true when instantiating the push object.
This is print a plain english payload to _debug/samplePush.txt, and report
any known errors to _debug/debug.txt

-- Debug Options End --




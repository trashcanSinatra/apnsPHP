CREATE DATABASE ios_push_app;
USE ios_push_app;


DROP TABLE IF EXISTS tokens;
CREATE TABLE tokens (
  device_token VARCHAR(150) NOT NULL,
  PRIMARY KEY  (`device_token`)
) ;


DROP TABLE IF EXISTS messages;
CREATE TABLE messages (
  message_id int(11) NOT NULL auto_increment,
  message varchar(45) NOT NULL,
  status varchar(10) NOT NULL,
  PRIMARY KEY  (`message_id`)
) AUTO_INCREMENT=7;








SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE onlinechatapp DEFAULT CHARACTER SET utf8;

GRANT ALL ON onlinechatapp.* TO 'admin'@'localhost' IDENTIFIED BY 'admin';

CREATE TABLE message (
   id  INTEGER NOT NULL AUTO_INCREMENT,
   content       VARCHAR(1000),
   user_id         VARCHAR(10),
   PRIMARY KEY(id)
) ENGINE=InnoDB CHARACTER SET=utf8;
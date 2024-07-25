CREATE DATABASE profile_management;
USE profile_management;
CREATE TABLE profile_details (
  id INT(11) NOT NULL AUTO_INCREMENT,
  role_id INT(11) NOT NULL,
  department VARCHAR(255) NOT NULL,
  semester VARCHAR(50) NOT NULL,
  college_name VARCHAR(255) NOT NULL,
  batch VARCHAR(50) NOT NULL,
  PRIMARY KEY (id)
);


ALTER TABLE profile_details ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL;
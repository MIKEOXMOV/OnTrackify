CREATE TABLE IF NOT EXISTS upload (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  fname varchar(255) NOT NULL,
  group_id int(11) NOT NULL,
  student_id int(11) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (group_id) REFERENCES groups(group_id),
  FOREIGN KEY (student_id) REFERENCES users(id)
);
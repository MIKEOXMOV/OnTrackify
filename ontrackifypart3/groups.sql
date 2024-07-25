CREATE TABLE groups (
    group_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    coordinator_id INT(11) DEFAULT NULL,
    project_id INT(11) DEFAULT NULL,
    group_size INT(11) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (coordinator_id),
    INDEX (project_id)
);
CREATE TABLE group_members (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    group_id INT(11) DEFAULT NULL,
    student_id INT(11) DEFAULT NULL
);
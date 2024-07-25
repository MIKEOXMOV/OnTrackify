CREATE TABLE ideas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    group_id INT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_idea (student_id, group_id),
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (group_id) REFERENCES groups(id)
);

ALTER TABLE ideas ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'pending';
-- Create notifications table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    recipient VARCHAR(20) NOT NULL,
    coordinator_id INT NOT NULL,
     date_time DATETIME NOT NULL,
    FOREIGN KEY (coordinator_id) REFERENCES coordinators(user_id)
);

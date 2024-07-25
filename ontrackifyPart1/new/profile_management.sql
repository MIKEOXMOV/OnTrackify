USE profile_management;

-- Add columns for 'name', 'email', 'register_or_faculty_id', and 'role'
ALTER TABLE profile_details
ADD COLUMN name VARCHAR(255) NOT NULL,
ADD COLUMN email VARCHAR(255) NOT NULL,
ADD COLUMN register_or_faculty_id VARCHAR(50) NOT NULL,
ADD COLUMN role VARCHAR(50) NOT NULL;

-- Modify existing columns if needed
ALTER TABLE profile_details
MODIFY COLUMN department VARCHAR(255) DEFAULT NULL,
MODIFY COLUMN semester VARCHAR(50) DEFAULT NULL,
MODIFY COLUMN college_name VARCHAR(255) DEFAULT NULL,
MODIFY COLUMN batch VARCHAR(50) DEFAULT NULL;
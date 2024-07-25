CREATE TABLE progress (
    group_id INT(11) NOT NULL,
    idea_submission TINYINT(1) DEFAULT 0,
    idea_confirmation TINYINT(1) DEFAULT 0,
    design_phase TINYINT(1) DEFAULT 0,
    task_appointment TINYINT(1) DEFAULT 0,
    zeroth_phase_presentation TINYINT(1) DEFAULT 0,
    objectives TINYINT(1) DEFAULT 0,
    system_architecture TINYINT(1) DEFAULT 0,
    ppt_making_first_presentation TINYINT(1) DEFAULT 0,
    first_presentation TINYINT(1) DEFAULT 0,
    fifty_percent_project_completed TINYINT(1) DEFAULT 0,
    seventy_five_percent_project_completed TINYINT(1) DEFAULT 0,
    hundred_percent_project_completed TINYINT(1) DEFAULT 0,
    internal_presentation TINYINT(1) DEFAULT 0,
    verified_report TINYINT(1) DEFAULT 0,
    external_presentation TINYINT(1) DEFAULT 0,
    PRIMARY KEY (group_id)
);
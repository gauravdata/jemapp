ALTER TABLE qu_g_tasks ADD name VARCHAR(255) NULL;
ALTER TABLE qu_g_tasks ADD progress_message TEXT NULL;
ALTER TABLE qu_g_tasks CHANGE params params LONGTEXT NULL DEFAULT NULL;  
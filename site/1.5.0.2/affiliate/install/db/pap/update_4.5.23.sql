CREATE TABLE qu_g_mail_templates_temp AS SELECT * FROM qu_g_mail_templates GROUP BY classname, accountid;
TRUNCATE qu_g_mail_templates;
INSERT INTO qu_g_mail_templates SELECT * FROM qu_g_mail_templates_temp;
DROP TABLE qu_g_mail_templates_temp;
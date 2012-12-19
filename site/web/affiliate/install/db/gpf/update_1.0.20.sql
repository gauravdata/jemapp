ALTER TABLE qu_g_mail_templates ADD created DATETIME;
UPDATE qu_g_mail_templates SET created=NOW() WHERE created = NULL;
ALTER TABLE qu_g_mail_templates CHANGE body body_html LONGTEXT NULL DEFAULT NULL;
ALTER TABLE qu_g_mail_templates ADD body_text LONGTEXT NULL ;
UPDATE qu_g_mail_templates SET body_text = body_html WHERE ishtmlformat = 'N';
UPDATE qu_g_mail_templates SET body_html = NULL WHERE ishtmlformat = 'N';
ALTER TABLE qu_g_mail_templates DROP ishtmlformat;
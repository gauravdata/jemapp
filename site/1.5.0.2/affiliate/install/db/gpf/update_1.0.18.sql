DELETE FROM qu_g_tasks WHERE classname='Gpf_Mail_OutboxRunner';
INSERT INTO qu_g_tasks (taskid ,accountid ,classname ,params ,progress ,datecreated ,datechanged ,datefinished ,pid ,name ,progress_message)
VALUES ('11111', 'default1', 'Gpf_Mail_OutboxRunner', NULL , NULL , '2009-05-13 13:38:57', NULL , NULL , NULL , 'Send pending mails' , NULL);
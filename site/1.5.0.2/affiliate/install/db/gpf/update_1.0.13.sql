CREATE TABLE qu_g_quicktasks (
    quicktaskid CHAR(16) NOT NULL,
    accountid CHAR(8),
    groupid CHAR(16),
    request LONGTEXT,
    validto DATETIME,
    CONSTRAINT PK_qu_g_quicktasks PRIMARY KEY (quicktaskid)
);

ALTER TABLE qu_g_quicktasks ADD CONSTRAINT qu_g_accounts_qu_g_quicktasks 
    FOREIGN KEY (accountid) REFERENCES qu_g_accounts (accountid);
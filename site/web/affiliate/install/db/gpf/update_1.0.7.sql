CREATE TABLE qu_nl_user_broadcasts (
    created DATETIME NOT NULL,
    broadcastid CHAR(8) NOT NULL,
    signupid VARCHAR(32) NOT NULL,
    outboxid INTEGER UNSIGNED,
    CONSTRAINT PK_qu_nl_user_broadcasts PRIMARY KEY (broadcastid, signupid)
);

CREATE TABLE qu_nl_newsletters (
    newsletterid VARCHAR(8) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    success_signup_url TEXT,
    double_optin CHAR(1) NOT NULL DEFAULT 'Y',
    mailaccountid VARCHAR(8),
    optin_templateid VARCHAR(8) NOT NULL,
    accountid CHAR(8) NOT NULL,
    CONSTRAINT PK_qu_nl_newsletters PRIMARY KEY (newsletterid)
);

CREATE TABLE qu_nl_newsletter_signups (
    signupid VARCHAR(32) NOT NULL,
    created DATETIME NOT NULL,
    subscribed DATETIME,
    unsubscribed DATETIME,
    signup_status CHAR(1) NOT NULL,
    ip VARCHAR(15),
    unsubscribe_reason TEXT,
    newsletterid VARCHAR(8) NOT NULL,
    accountuserid CHAR(8) NOT NULL,
    CONSTRAINT PK_qu_nl_newsletter_signups PRIMARY KEY (signupid)
);

CREATE TABLE qu_nl_user_followups (
    created DATETIME NOT NULL,
    followupid CHAR(8) NOT NULL,
    signupid VARCHAR(32) NOT NULL,
    outboxid INTEGER UNSIGNED,
    CONSTRAINT PK_qu_nl_user_followups PRIMARY KEY (followupid, signupid)
);

CREATE TABLE qu_nl_followups (
    followupid CHAR(8) NOT NULL,
    delay_days INTEGER UNSIGNED NOT NULL DEFAULT 0,
    followup_status CHAR(1) NOT NULL,
    delivery_hour SMALLINT NOT NULL DEFAULT 12,
    newsletterid VARCHAR(8) NOT NULL,
    templateid VARCHAR(8) NOT NULL,
    modified DATETIME NOT NULL,
    CONSTRAINT PK_qu_nl_followups PRIMARY KEY (followupid)
);

CREATE TABLE qu_nl_broadcasts (
    broadcastid CHAR(8) NOT NULL,
    created DATETIME NOT NULL,
    scheduled DATETIME,
    broadcast_status CHAR(1) NOT NULL,
    newsletterid VARCHAR(8) NOT NULL,
    templateid VARCHAR(8) NOT NULL,
    modified DATETIME NOT NULL,
    CONSTRAINT PK_qu_nl_broadcasts PRIMARY KEY (broadcastid)
);

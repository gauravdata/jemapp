CREATE TABLE qu_pap_impressions0 (
    impressionid INTEGER NOT NULL AUTO_INCREMENT,
    date DATETIME,
    rtype CHAR(1),
    userid VARCHAR(128),
    bannerid CHAR(8),
    channel VARCHAR(10),
    countrycode CHAR(2),
    data1 VARCHAR(20),
    data2 VARCHAR(20),
    CONSTRAINT PK_qu_pap_impressions0 PRIMARY KEY (impressionid)
);

CREATE TABLE qu_pap_impressions1 (
    impressionid INTEGER NOT NULL AUTO_INCREMENT,
    date DATETIME,
    rtype CHAR(1),
    userid VARCHAR(128),
    bannerid CHAR(8),
    channel VARCHAR(10),
    countrycode CHAR(2),
    data1 VARCHAR(20),
    data2 VARCHAR(20),
    CONSTRAINT PK_qu_pap_impressions1 PRIMARY KEY (impressionid)
);

CREATE TABLE qu_pap_impressions2 (
    impressionid INTEGER NOT NULL AUTO_INCREMENT,
    date DATETIME,
    rtype CHAR(1),
    userid VARCHAR(128),
    bannerid CHAR(8),
    channel VARCHAR(10),
    countrycode CHAR(2),
    data1 VARCHAR(20),
    data2 VARCHAR(20),
    CONSTRAINT PK_qu_pap_impressions2 PRIMARY KEY (impressionid)
);
ALTER TABLE qu_pap_visitoraffiliates ADD accountid CHAR(8) NOT NULL;
UPDATE qu_pap_visitoraffiliates SET accountid = 'default1';

ALTER TABLE qu_pap_visits0 ADD accountid CHAR(8) NOT NULL;
UPDATE qu_pap_visits0 SET accountid = 'default1';
ALTER TABLE qu_pap_visits1 ADD accountid CHAR(8) NOT NULL;
UPDATE qu_pap_visits1 SET accountid = 'default1';
ALTER TABLE qu_pap_visits2 ADD accountid CHAR(8) NOT NULL;
UPDATE qu_pap_visits2 SET accountid = 'default1';
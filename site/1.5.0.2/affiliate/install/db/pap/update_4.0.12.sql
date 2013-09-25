# ---------------------------------------------------------------------- #
# Modify table "qu_pap_users"                                           #
# ---------------------------------------------------------------------- #

UPDATE qu_pap_users SET minimumpayout = '300' WHERE minimumpayout IS NULL;

ALTER TABLE qu_pap_users CHANGE minimumpayout minimumpayout VARCHAR( 20 ) NOT NULL DEFAULT '300';
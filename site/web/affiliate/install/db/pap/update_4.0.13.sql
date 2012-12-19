# ---------------------------------------------------------------------- #
# Modify table "qu_pap_users"                                           #
# ---------------------------------------------------------------------- #

ALTER TABLE qu_pap_transactions CHANGE payoutstatus payoutstatus CHAR( 1 ) NOT NULL DEFAULT 'U';
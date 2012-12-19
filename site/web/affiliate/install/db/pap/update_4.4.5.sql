UPDATE qu_pap_transactions u 
INNER JOIN (SELECT parenttransid, saleid FROM qu_pap_transactions WHERE parenttransid IS NOT NULL AND parenttransid <> '' AND tier = '2') t ON u.transid = t.parenttransid
SET u.saleid = t.saleid;
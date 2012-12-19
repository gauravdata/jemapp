UPDATE qu_pap_transactions t LEFT JOIN qu_pap_campaigns c ON t.campaignid = c.campaignid
SET t.accountid = c.accountid
WHERE (t.campaignid IS NOT NULL) AND (t.campaignid <> '')
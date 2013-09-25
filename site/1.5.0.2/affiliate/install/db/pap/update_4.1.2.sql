INSERT INTO qu_pap_commissiontypes (
commtypeid,
rtype,
rstatus,
approval,
zeroorderscommission) 
VALUES ('73cb6c9a','F','E','A','N');

INSERT INTO qu_pap_commissions (
commissionid,
tier,
subtype,
commissiontype,
commissionvalue,
commissiongroupid,
commtypeid) 
SELECT
'8bf9655b','1','N','$',value,NULL,'73cb6c9a' 
FROM qu_g_settings WHERE name = 'referralCommission'; 

DELETE FROM qu_g_settings WHERE name = 'referralCommission' LIMIT 1;
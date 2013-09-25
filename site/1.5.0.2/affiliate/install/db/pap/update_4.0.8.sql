INSERT INTO `qu_g_formfields` (
`formfieldid` ,
`accountid` ,
`sectionid` ,
`formid` ,
`code` ,
`name` ,
`rtype` ,
`rstatus` ,
`availablevalues` ,
`rorder`
)
SELECT
NULL , accountid, NULL , 'affiliateForm', 'refid', 'Referral ID', 'T', 'M', NULL , NULL 
FROM qu_g_accounts;

INSERT INTO `qu_g_formfields` (
`formfieldid` ,
`accountid` ,
`sectionid` ,
`formid` ,
`code` ,
`name` ,
`rtype` ,
`rstatus` ,
`availablevalues` ,
`rorder`
)
SELECT
NULL , accountid, NULL , 'affiliateForm', 'parentuserid', 'Parent affiliate', 'T', 'M', NULL , NULL 
FROM qu_g_accounts;

ALTER TABLE qu_g_accounts ADD dateinserted DATETIME NULL;
UPDATE qu_g_accounts SET dateinserted = (SELECT done FROM qu_g_versions ORDER BY done LIMIT 1);
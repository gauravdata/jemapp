UPDATE qu_g_gadgets SET name = 'Quick start actions' WHERE name = 'Quick actions';
UPDATE qu_g_gadgets SET name = 'Search' WHERE name = 'Quick search';
UPDATE qu_g_gadgets SET name = CONCAT('##', name, '##') WHERE rtype = 'C';
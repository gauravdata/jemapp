UPDATE qu_pap_affiliatetrackingcodes SET rtype = 'H' WHERE (rtype IS NULL OR rtype = '') AND code NOT LIKE '%document.createElement%';
UPDATE qu_pap_affiliatetrackingcodes SET rtype = 'S' WHERE rtype IS NULL OR rtype = '';
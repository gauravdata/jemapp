UPDATE qu_pap_transactions 
SET firstclickreferer = concat('https://', substring(firstclickreferer, 3))
WHERE firstclickreferer LIKE 'S\_%';

UPDATE qu_pap_transactions
SET firstclickreferer = concat('http://', substring(firstclickreferer, 3))
WHERE firstclickreferer LIKE 'H\_%';

UPDATE qu_pap_transactions 
SET lastclickreferer = concat('https://', substring(lastclickreferer, 3))
WHERE lastclickreferer LIKE 'S\_%';

UPDATE qu_pap_transactions 
SET lastclickreferer = concat('http://', substring(lastclickreferer, 3))
WHERE lastclickreferer LIKE 'H\_%';

UPDATE qu_pap_visitoraffiliates 
SET referrerurl = concat('https://', substring(referrerurl, 3))
WHERE referrerurl LIKE 'S\_%';

UPDATE qu_pap_visitoraffiliates 
SET referrerurl = concat('http://', substring(referrerurl, 3))
WHERE referrerurl LIKE 'H\_%';
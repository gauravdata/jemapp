UPDATE qu_g_formfields SET availablevalues=
concat(
  '[["id","value"],',
  substring(availablevalues,16)
) 
WHERE availablevalues LIKE '[["id","name"],%' AND rtype='L'
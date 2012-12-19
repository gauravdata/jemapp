INSERT INTO qu_pap_clicks (userid,campaignid,bannerid,parentbannerid,countrycode,cdata1,cdata2,channel,dateinserted,raw,uniq,declined) SELECT userid, campaignid, bannerid, parentbannerid, countrycode, cdata1, cdata2, channel, DATE_FORMAT(month, '%Y-%m-25 12:00:00') as dateinserted, raw_25 as raw, unique_25 as uniq, declined_25 as `declined` FROM qu_pap_monthlyclicks WHERE DAY(LAST_DAY(month)) >= 25 AND (raw_25 > 0 OR unique_25 > 0 OR declined_25 > 0);
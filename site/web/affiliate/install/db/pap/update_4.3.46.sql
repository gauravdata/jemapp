INSERT INTO qu_pap_clicks (userid,campaignid,bannerid,parentbannerid,countrycode,cdata1,cdata2,channel,dateinserted,raw,uniq,declined) SELECT userid, campaignid, bannerid, parentbannerid, countrycode, cdata1, cdata2, channel, CONCAT(DATE(day), ' 12:00:00') as dateinserted, raw_12 as raw, unique_12 as uniq, declined_12 as `declined` FROM qu_pap_dailyclicks WHERE raw_12 > 0 OR unique_12 > 0 OR declined_12 > 0 ;
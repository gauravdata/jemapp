INSERT INTO qu_pap_impressions (userid,campaignid,bannerid,parentbannerid,countrycode,cdata1,cdata2,channel,dateinserted,raw,uniq) SELECT userid, campaignid, bannerid, parentbannerid, countrycode, cdata1, cdata2, channel, CONCAT(DATE(day), ' 12:00:00') as dateinserted, raw_12 as raw, unique_12 as uniq FROM qu_pap_dailyimpressions WHERE raw_12 > 0 OR unique_12 > 0;
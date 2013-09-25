INSERT INTO qu_pap_impressions (userid,campaignid,bannerid,parentbannerid,countrycode,cdata1,cdata2,channel,dateinserted,raw,uniq) SELECT userid, campaignid, bannerid, parentbannerid, countrycode, cdata1, cdata2, channel, DATE_FORMAT(month, '%Y-%m-22 12:00:00') as dateinserted, raw_22 as raw, unique_22 as uniq FROM qu_pap_monthlyimpressions WHERE DAY(LAST_DAY(month)) >= 22 AND (raw_22 > 0 OR unique_22 > 0);
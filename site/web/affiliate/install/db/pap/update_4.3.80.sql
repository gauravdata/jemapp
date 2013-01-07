INSERT INTO qu_pap_impressions (userid,campaignid,bannerid,parentbannerid,countrycode,cdata1,cdata2,channel,dateinserted,raw,uniq) SELECT userid, campaignid, bannerid, parentbannerid, countrycode, cdata1, cdata2, channel, DATE_FORMAT(month, '%Y-%m-23 12:00:00') as dateinserted, raw_23 as raw, unique_23 as uniq FROM qu_pap_monthlyimpressions WHERE DAY(LAST_DAY(month)) >= 23 AND (raw_23 > 0 OR unique_23 > 0);
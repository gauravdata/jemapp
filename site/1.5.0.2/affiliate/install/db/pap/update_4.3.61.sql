INSERT INTO qu_pap_impressions (userid,campaignid,bannerid,parentbannerid,countrycode,cdata1,cdata2,channel,dateinserted,raw,uniq) SELECT userid, campaignid, bannerid, parentbannerid, countrycode, cdata1, cdata2, channel, DATE_FORMAT(month, '%Y-%m-4 12:00:00') as dateinserted, raw_4 as raw, unique_4 as uniq FROM qu_pap_monthlyimpressions WHERE DAY(LAST_DAY(month)) >= 4 AND (raw_4 > 0 OR unique_4 > 0);
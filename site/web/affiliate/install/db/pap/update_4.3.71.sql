INSERT INTO qu_pap_impressions (userid,campaignid,bannerid,parentbannerid,countrycode,cdata1,cdata2,channel,dateinserted,raw,uniq) SELECT userid, campaignid, bannerid, parentbannerid, countrycode, cdata1, cdata2, channel, DATE_FORMAT(month, '%Y-%m-14 12:00:00') as dateinserted, raw_14 as raw, unique_14 as uniq FROM qu_pap_monthlyimpressions WHERE DAY(LAST_DAY(month)) >= 14 AND (raw_14 > 0 OR unique_14 > 0);
ALTER TABLE `qu_pap_dailyclicks` CHANGE `cdata1` `cdata1` VARCHAR( 40 ) ,
CHANGE `cdata2` `cdata2` VARCHAR( 40 ) ; 

ALTER TABLE `qu_pap_dailyimpressions` CHANGE `cdata1` `cdata1` VARCHAR( 40 ) ,
CHANGE `cdata2` `cdata2` VARCHAR( 40 ) ;

ALTER TABLE `qu_pap_monthlyclicks` CHANGE `cdata1` `cdata1` VARCHAR( 40 ) ,
CHANGE `cdata2` `cdata2` VARCHAR( 40 ) ; 

ALTER TABLE `qu_pap_monthlyimpressions` CHANGE `cdata1` `cdata1` VARCHAR( 40 ) ,
CHANGE `cdata2` `cdata2` VARCHAR( 40 ) ; 

ALTER TABLE `qu_pap_rawclicks` CHANGE `cdata1` `cdata1` VARCHAR( 40 ) ,
CHANGE `cdata2` `cdata2` VARCHAR( 40 ) ;

ALTER TABLE `qu_pap_impressions0` CHANGE `data1` `data1` VARCHAR( 40 ) ,
CHANGE `data2` `data2` VARCHAR( 40 ) ;

ALTER TABLE `qu_pap_impressions1` CHANGE `data1` `data1` VARCHAR( 40 ) ,
CHANGE `data2` `data2` VARCHAR( 40 ) ;

ALTER TABLE `qu_pap_impressions2` CHANGE `data1` `data1` VARCHAR( 40 ) ,
CHANGE `data2` `data2` VARCHAR( 40 ) ;
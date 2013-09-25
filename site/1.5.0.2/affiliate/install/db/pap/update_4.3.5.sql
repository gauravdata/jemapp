update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'impressionsRaw' where vc.name='imps' and v.viewtype='Pap_Merchants_Banner_BannersGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'clicksRaw' where vc.name='clicks' and v.viewtype='Pap_Merchants_Banner_BannersGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'ctrRaw' where vc.name='ctr' and v.viewtype='Pap_Merchants_Banner_BannersGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'salesCount' where vc.name='sales' and v.viewtype='Pap_Merchants_Banner_BannersGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'scrRaw' where vc.name='scr' and v.viewtype='Pap_Merchants_Banner_BannersGrid';

update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'salesCount' where vc.name='sales' and v.viewtype='Pap_Merchants_User_AffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'salesTotal' where vc.name='totalcost' and v.viewtype='Pap_Merchants_User_AffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'clicksRaw' where vc.name='rawclicks' and v.viewtype='Pap_Merchants_User_AffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'clicksUnique' where vc.name='uniqueclicks' and v.viewtype='Pap_Merchants_User_AffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'impressionsRaw' where vc.name='rawimpressions' and v.viewtype='Pap_Merchants_User_AffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'impressionsUnique' where vc.name='uniqueimpressions' and v.viewtype='Pap_Merchants_User_AffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'ctrRaw' where vc.name='clickthroughratio' and v.viewtype='Pap_Merchants_User_AffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'scrRaw' where vc.name='conversionratio' and v.viewtype='Pap_Merchants_User_AffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'avgCommissionPerClick' where vc.name='avgcommissionperclick' and v.viewtype='Pap_Merchants_User_AffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'avgCommissionPerImp' where vc.name='avgcommissionperimpression' and v.viewtype='Pap_Merchants_User_AffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'avgAmountOfOrder' where vc.name='amountOfOrder' and v.viewtype='Pap_Merchants_User_AffiliatesGrid';

update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'salesCount' where vc.name='sales' and v.viewtype='Pap_Merchants_User_TopAffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'salesTotal' where vc.name='totalcost' and v.viewtype='Pap_Merchants_User_TopAffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'clicksRaw' where vc.name='rawclicks' and v.viewtype='Pap_Merchants_User_TopAffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'clicksUnique' where vc.name='uniqueclicks' and v.viewtype='Pap_Merchants_User_TopAffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'impressionsRaw' where vc.name='rawimpressions' and v.viewtype='Pap_Merchants_User_TopAffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'impressionsUnique' where vc.name='uniqueimpressions' and v.viewtype='Pap_Merchants_User_TopAffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'ctrRaw' where vc.name='clickthroughratio' and v.viewtype='Pap_Merchants_User_TopAffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'scrRaw' where vc.name='conversionratio' and v.viewtype='Pap_Merchants_User_TopAffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'avgCommissionPerClick' where vc.name='avgcommissionperclick' and v.viewtype='Pap_Merchants_User_TopAffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'avgCommissionPerImp' where vc.name='avgcommissionperimpression' and v.viewtype='Pap_Merchants_User_TopAffiliatesGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'avgAmountOfOrder' where vc.name='amountOfOrder' and v.viewtype='Pap_Merchants_User_TopAffiliatesGrid';

update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'impressionsRaw' where vc.name='impressions' and v.viewtype='Pap_Affiliates_Reports_ChannelStatsGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'clicksRaw' where vc.name='clicks' and v.viewtype='Pap_Affiliates_Reports_ChannelStatsGrid';
update qu_g_view_columns vc inner join qu_g_views v on vc.viewid = v.viewid set vc.name = 'ctrRaw' where vc.name='ctr' and v.viewtype='Pap_Affiliates_Reports_ChannelStatsGrid';
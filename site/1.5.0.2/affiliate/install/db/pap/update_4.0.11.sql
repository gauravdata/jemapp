# ---------------------------------------------------------------------- #
# Modify table "qu_pap_bannersinrotators"                                  #
# ---------------------------------------------------------------------- #

ALTER TABLE `qu_pap_bannersinrotators` CHANGE `all_imps` `all_imps` INT( 11 ) NULL DEFAULT '0';
ALTER TABLE `qu_pap_bannersinrotators` CHANGE `uniq_imps` `uniq_imps` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0';
ALTER TABLE `qu_pap_bannersinrotators` CHANGE `clicks` `clicks` INT( 11 ) NULL DEFAULT '0';

UPDATE `qu_pap_bannersinrotators` SET `all_imps` = '0',`uniq_imps` = '0',`clicks` = '0';
#------------------------------------------------------------------------#
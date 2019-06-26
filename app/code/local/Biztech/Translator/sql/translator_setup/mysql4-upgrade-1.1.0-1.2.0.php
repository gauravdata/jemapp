<?php
$this->startSetup();

$this->run("
	alter table `{$this->getTable('translator_logcron')}` change column remain_limit `remain_limit` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '0' COMMENT  'Remaining Daily Limit';

");

$this->endSetup();
ALTER TABLE qu_g_languages ADD thousandsseparator CHAR(1) NULL DEFAULT NULL AFTER timeformat,
	ADD decimalseparator CHAR(1) NULL DEFAULT NULL AFTER thousandsseparator;
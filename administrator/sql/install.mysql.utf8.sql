CREATE TABLE IF NOT EXISTS `#__afarosadelsvents_families` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`nom_nin` VARCHAR(255)  NOT NULL ,
`comunitat_nin` VARCHAR(255)  NOT NULL ,
`data_naixement` DATETIME NULL  DEFAULT NULL ,
`comentaris` TEXT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS `#__afarosadelsvents_menjador` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',

`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`comunitat_nin_menjador` VARCHAR(255)  NULL  DEFAULT "",
`id_nin_menjador` INT NULL  DEFAULT 0,
`nom_menjador` VARCHAR(255)  NOT NULL ,
`data_menjador` DATE NOT NULL ,
`opcio_menjador` VARCHAR(255)  NOT NULL  DEFAULT "Menú",
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_spanish_ci;

CREATE INDEX `#__afarosadelsvents_menjador_id_nin_menjador` ON `#__afarosadelsvents_menjador`(`id_nin_menjador`);

CREATE INDEX `#__afarosadelsvents_menjador_nom_menjador` ON `#__afarosadelsvents_menjador`(`nom_menjador`);

CREATE INDEX `#__afarosadelsvents_menjador_data_menjador` ON `#__afarosadelsvents_menjador`(`data_menjador`);


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `content_history_options`)
SELECT * FROM ( SELECT 'Family','com_afarosadelsvents.family','{"special":{"dbtable":"#__afarosadelsvents_families","key":"id","type":"FamilyTable","prefix":"Joomla\\\\Component\\\\Afarosadelsvents\\\\Administrator\\\\Table\\\\"}}', CASE 
                                    WHEN 'rules' is null THEN ''
                                    ELSE ''
                                    END as rules, CASE 
                                    WHEN 'field_mappings' is null THEN ''
                                    ELSE ''
                                    END as field_mappings, '{"formFile":"administrator\/components\/com_afarosadelsvents\/forms\/family.xml", "hideFields":["checked_out","checked_out_time","params","language" ,"comentaris"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_afarosadelsvents.family')
) LIMIT 1;

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `field_mappings`, `content_history_options`)
SELECT * FROM ( SELECT 'Menjador','com_afarosadelsvents.menjador','{"special":{"dbtable":"#__afarosadelsvents_menjador","key":"id","type":"MenjadorTable","prefix":"Joomla\\\\Component\\\\Afarosadelsvents\\\\Administrator\\\\Table\\\\"}}', CASE 
                                    WHEN 'rules' is null THEN ''
                                    ELSE ''
                                    END as rules, CASE 
                                    WHEN 'field_mappings' is null THEN ''
                                    ELSE ''
                                    END as field_mappings, '{"formFile":"administrator\/components\/com_afarosadelsvents\/forms\/menjador.xml", "hideFields":["checked_out","checked_out_time","params","language"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"nom_menjador","targetTable":"#__afarosadelsvents_families","targetColumn":"nom_nin","displayColumn":"nom_nin"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_afarosadelsvents.menjador')
) LIMIT 1;

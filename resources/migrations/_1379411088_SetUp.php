<?php

use Message\Cog\Migration\Adapter\MySQL\Migration;

class _1379411088_SetUp extends Migration
{
	public function up()
	{
		$this->run("
			CREATE TABLE `file` (
			  `file_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `created_at` int(11) unsigned NOT NULL,
			  `created_by` int(11) unsigned DEFAULT NULL,
			  `updated_at` int(11) unsigned DEFAULT NULL,
			  `updated_by` int(11) unsigned DEFAULT NULL,
			  `deleted_at` int(11) unsigned DEFAULT NULL,
			  `deleted_by` int(11) unsigned DEFAULT NULL,
			  `url` varchar(255) NOT NULL DEFAULT '',
			  `name` varchar(255) NOT NULL DEFAULT '',
			  `extension` varchar(10) NOT NULL DEFAULT '',
			  `file_size` int(11) unsigned NOT NULL,
			  `type_id` int(11) unsigned NOT NULL,
			  `checksum` char(32) DEFAULT NULL,
			  `preview_url` varchar(255) DEFAULT NULL,
			  `dimension_x` int(11) unsigned DEFAULT NULL,
			  `dimension_y` int(11) unsigned DEFAULT NULL,
			  `alt_text` text,
			  `duration` int(11) unsigned DEFAULT NULL,
			  PRIMARY KEY (`file_id`),
			  UNIQUE KEY `url` (`url`),
			  UNIQUE KEY `checksum` (`checksum`),
			  KEY `created_by` (`created_by`),
			  KEY `updated_by` (`updated_by`),
			  KEY `deleted_by` (`deleted_by`),
			  KEY `type_id` (`type_id`),
			  KEY `created_at` (`created_at`),
			  KEY `updated_at` (`updated_at`),
			  KEY `deleted_at` (`deleted_at`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");

		$this->run("
			CREATE TABLE `file_tag` (
			  `file_id` int(11) unsigned NOT NULL,
			  `tag_name` varchar(255) NOT NULL DEFAULT '',
			  PRIMARY KEY (`file_id`,`tag_name`),
			  KEY `file_id` (`file_id`),
			  KEY `tag_name` (`tag_name`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");

		$this->run("
			CREATE TABLE `file_translation` (
			  `file_id` int(11) unsigned NOT NULL,
			  `locale` varchar(50) NOT NULL DEFAULT '',
			  `created_at` int(11) unsigned NOT NULL,
			  `created_by` int(11) unsigned DEFAULT NULL,
			  `updated_at` int(11) unsigned DEFAULT NULL,
			  `updated_by` int(11) unsigned DEFAULT NULL,
			  `deleted_at` int(11) unsigned DEFAULT NULL,
			  `deleted_by` int(11) unsigned DEFAULT NULL,
			  `alt_text` text,
			  PRIMARY KEY (`file_id`,`locale`),
			  KEY `created_at` (`created_at`),
			  KEY `created_by` (`created_by`),
			  KEY `updated_at` (`updated_at`),
			  KEY `updated_by` (`updated_by`),
			  KEY `deleted_at` (`deleted_at`),
			  KEY `deleted_by` (`deleted_by`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
	}

	public function down()
	{
		$this->run('
			DROP TABLE IF EXISTS
				`file`
		');
		$this->run('
			DROP TABLE IF EXISTS
				`file_tag`
		');
		$this->run('
			DROP TABLE IF EXISTS
				`file_translation`
		');
	}
}

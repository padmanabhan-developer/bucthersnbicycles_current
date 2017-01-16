CREATE TABLE IF NOT EXISTS `gc_part_types` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '0',
  `is_required` int(1) NOT NULL DEFAULT '0',
  `order` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Group for external parts';


CREATE TABLE IF NOT EXISTS `gc_images` (
  `img_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(500) NOT NULL DEFAULT '0',
  PRIMARY KEY (`img_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='image bicycle';


CREATE TABLE IF NOT EXISTS `gc_main_parts` (
  `main_part_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '0',
  `descr` text NOT NULL,
  `cost` int(10) NOT NULL DEFAULT '0',
  `in_stock` INT(10) NOT NULL DEFAULT 0,
  `order` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`main_part_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Main bicycle details';


CREATE TABLE IF NOT EXISTS `gc_external_parts` (
  `external_part_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cost` int(10) NOT NULL DEFAULT '0',
  `in_stock` INT(10) NOT NULL DEFAULT 0,
  `name` varchar(200) NOT NULL,
  `descr` text NOT NULL,
  `type_id` int(10) unsigned DEFAULT NULL,
  `order` int(10) NOT NULL,
  PRIMARY KEY (`external_part_id`),
  KEY `fk_external_parts_type` (`type_id`),
  CONSTRAINT `fk_external_parts_type` FOREIGN KEY (`type_id`) REFERENCES `gc_part_types` (`type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Additional bicycle parts';


CREATE TABLE IF NOT EXISTS `gc_conf_head` (
  `conf_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `img_id` int(10) unsigned DEFAULT NULL,
  `main_part_id` int(10) unsigned NOT NULL,
  `parts_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`conf_id`),
  KEY `fk_conf_head` (`img_id`),
  CONSTRAINT `fk_head_conf_main_part` FOREIGN KEY (`main_part_id`) REFERENCES `gc_main_parts` (`main_part_id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT `fk_conf_head` FOREIGN KEY (`img_id`) REFERENCES `gc_images` (`img_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Heads of configurations';


CREATE TABLE IF NOT EXISTS `gc_conf_body` (
  `conf_body_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `conf_id` int(10) unsigned NOT NULL DEFAULT '0',
  `external_part_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`conf_body_id`),
  KEY `fk_conf_body_head_id` (`conf_id`),
  KEY `fk_conf_body_part_id` (`external_part_id`),
  CONSTRAINT `fk_conf_body_head_id` FOREIGN KEY (`conf_id`) REFERENCES `gc_conf_head` (`conf_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_conf_body_part_id` FOREIGN KEY (`external_part_id`) REFERENCES `gc_external_parts` (`external_part_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Body of configurations';


CREATE TABLE IF NOT EXISTS `gc_user_conf_head` (
  `conf_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `img_id` int(10) unsigned DEFAULT NULL,
  `main_part_id` int(10) unsigned NOT NULL,
  `parts_count` int(10) unsigned NOT NULL DEFAULT '0',
  `conf_date`  datetime NOT NULL,
  PRIMARY KEY (`conf_id`),
  KEY `fk_conf_head` (`img_id`),
  CONSTRAINT `fk_user_conf_head` FOREIGN KEY (`img_id`) REFERENCES `gc_images` (`img_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Heads of user configurations';


CREATE TABLE IF NOT EXISTS `gc_user_conf_body` (
  `conf_body_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `conf_id` int(10) unsigned NOT NULL DEFAULT '0',
  `external_part_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`conf_body_id`),
  KEY `fk_conf_body_head_id` (`conf_id`),
  KEY `fk_conf_body_part_id` (`external_part_id`),
  CONSTRAINT `fk_user_conf_body_head_id` FOREIGN KEY (`conf_id`) REFERENCES `gc_user_conf_head` (`conf_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_user_conf_body_part_id` FOREIGN KEY (`external_part_id`) REFERENCES `gc_external_parts` (`external_part_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Body of user configurations';

CREATE TABLE IF NOT EXISTS `gc_external_parts_images` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `img_id` INT(10) UNSIGNED NOT NULL COMMENT 'referenced from table gc_images',
  `external_part_id` INT(10) UNSIGNED NOT NULL COMMENT 'referenced from table gc_external_parts',
  PRIMARY KEY (`id`),
  KEY `fk_gc_external_parts_image_id` (`img_id`),
  KEY `fk_gc_external_parts_part_id` (`external_part_id`),
  CONSTRAINT `fk_gc_external_parts_image_id` FOREIGN KEY (`img_id`) REFERENCES `gc_images` (`img_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_gc_external_parts_part_id` FOREIGN KEY (`external_part_id`) REFERENCES `gc_external_parts` (`external_part_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB CHARSET=utf8 COMMENT='External parts images';

create table if not exists `gc_countries`(
`country_id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT, 
`country_name` VARCHAR(32) NOT NULL UNIQUE
) engine 'InnoDB' default charset=utf8 comment='country dictionary';

create table if not exists `gc_regions`(
	`id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`country_id` INT UNSIGNED NOT NULL,
	`region_name` VARCHAR(32) NOT NULL UNIQUE,
	constraint `fk_country_id` foreign key(`country_id`) references `gc_countries`(`country_id`) on delete no action on update no action
) engine 'InnoDB' default charset=utf8 comment='regions dictionary';

create table if not exists `gc_delivery_services`(
`ds_id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
`delivery_service_name` VARCHAR(32) NOT NULL UNIQUE
) engine 'InnoDB' default charset=utf8 comment='delivery services dictionary';

create table if not exists `gc_delivery_costs`(
`id` INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
`region_id` INT UNSIGNED NOT NULL, 
`delivery_service_id` INT UNSIGNED NOT NULL,
`delivery_cost` FLOAT NOT NULL,
`currency` VARCHAR(3) NOT NULL,
constraint `fk_region_id` foreign key(`region_id`) references `gc_regions`(`id`) on delete no action on update no action,
constraint `fk_delivery_service_id` foreign key(`delivery_service_id`) references `gc_delivery_services`(`ds_id`) on delete no action on update no action
) engine 'InnoDB' default charset=utf8 comment='delivery cost';


SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';


DELIMITER //


CREATE TRIGGER `T_BD_CONF_BODY` BEFORE DELETE ON `gc_conf_body` FOR EACH ROW BEGIN
	update gc_conf_head h set h.parts_count = (h.parts_count - 1) where h.conf_id = OLD.conf_id; 
END//


DELIMITER ;


SET SQL_MODE=@OLDTMP_SQL_MODE;


SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `T_BD_USER_CONF_BODY` BEFORE DELETE ON `gc_user_conf_body` FOR EACH ROW BEGIN
	update gc_user_conf_head h set h.parts_count = (h.parts_count - 1) where h.conf_id = OLD.conf_id; 
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `T_BI_CONF_BODY` BEFORE INSERT ON `gc_conf_body` FOR EACH ROW BEGIN
	update gc_conf_head h set h.parts_count = (h.parts_count + 1) where h.conf_id = NEW.conf_id;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;


SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `T_BI_USER_CONF_BODY` BEFORE INSERT ON `gc_user_conf_body` FOR EACH ROW BEGIN
	update gc_user_conf_head h set h.parts_count = (h.parts_count + 1) where h.conf_id = NEW.conf_id; 
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;
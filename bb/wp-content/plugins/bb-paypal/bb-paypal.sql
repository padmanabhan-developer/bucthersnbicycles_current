CREATE TABLE IF NOT EXISTS `bbpp_orders` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`payment_id` varchar(32) NOT NULL,
	`order_created_time` datetime NOT NULL,
	`user_configuration_id` int(10) UNSIGNED NOT NULL,
	`configuration_name` VARCHAR(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='B&B PayPal orders';

CREATE TABLE IF NOT EXISTS `bbpp_payments` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`payment_id` varchar(32) NOT NULL,
	`create_time` datetime NOT NULL,
	`state` varchar(16) NOT NULL,
	`total_price` float NOT NULL,
	`currency` varchar(6) NOT NULL,
	`payment_details` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='B&B PayPal payments';
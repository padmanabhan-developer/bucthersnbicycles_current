-- --------------------------------------------------------
-- Хост:                         10.222.0.77
-- Версия сервера:               5.1.71 - Source distribution
-- ОС Сервера:                   redhat-linux-gnu
-- HeidiSQL Версия:              8.2.0.4675
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
-- Дамп данных таблицы bb_dev.gc_conf_body: ~5 rows (приблизительно)
DELETE FROM `gc_conf_body`;
/*!40000 ALTER TABLE `gc_conf_body` DISABLE KEYS */;
INSERT INTO `gc_conf_body` (`conf_body_id`, `conf_id`, `external_part_id`) VALUES
	(1, 1, 1),
	(2, 1, 3),
	(3, 2, 1),
	(5, 2, 3),
	(6, 2, 6);
/*!40000 ALTER TABLE `gc_conf_body` ENABLE KEYS */;

-- Дамп данных таблицы bb_dev.gc_conf_head: ~2 rows (приблизительно)
DELETE FROM `gc_conf_head`;
/*!40000 ALTER TABLE `gc_conf_head` DISABLE KEYS */;
INSERT INTO `gc_conf_head` (`conf_id`, `img_id`, `main_part_id`, `parts_count`) VALUES
	(1, 1, 1, 2),
	(2, 2, 2, 3);
/*!40000 ALTER TABLE `gc_conf_head` ENABLE KEYS */;

-- Дамп данных таблицы bb_dev.gc_external_parts: ~6 rows (приблизительно)
DELETE FROM `gc_external_parts`;
/*!40000 ALTER TABLE `gc_external_parts` DISABLE KEYS */;
INSERT INTO `gc_external_parts` (`external_part_id`, `cost`, `name`, `descr`, `type_id`, `order`) VALUES
	(1, 10, 'white', '', 1, 0),
	(2, 20, 'black', '', 1, 0),
	(3, 30, 'white', '', 2, 0),
	(4, 40, 'black', '', 2, 0),
	(5, 50, 'Gates Carbon Drive', '', NULL, 0),
	(6, 60, 'Black hood', '', NULL, 0);
/*!40000 ALTER TABLE `gc_external_parts` ENABLE KEYS */;

-- Дамп данных таблицы bb_dev.gc_images: ~2 rows (приблизительно)
DELETE FROM `gc_images`;
/*!40000 ALTER TABLE `gc_images` DISABLE KEYS */;
INSERT INTO `gc_images` (`img_id`, `path`) VALUES
	(1, '/wp-content/uploads/2014/02/Selection_001.png'),
	(2, '/wp-content/uploads/2014/02/Selection_002.png');
/*!40000 ALTER TABLE `gc_images` ENABLE KEYS */;

-- Дамп данных таблицы bb_dev.gc_main_parts: ~2 rows (приблизительно)
DELETE FROM `gc_main_parts`;
/*!40000 ALTER TABLE `gc_main_parts` DISABLE KEYS */;
INSERT INTO `gc_main_parts` (`main_part_id`, `name`, `descr`, `cost`, `order`) VALUES
	(1, 'MK-1', '', 100, 1),
	(2, 'MK-2', '', 200, 2);
/*!40000 ALTER TABLE `gc_main_parts` ENABLE KEYS */;

-- Дамп данных таблицы bb_dev.gc_part_types: ~2 rows (приблизительно)
DELETE FROM `gc_part_types`;
/*!40000 ALTER TABLE `gc_part_types` DISABLE KEYS */;
INSERT INTO `gc_part_types` (`type_id`, `name`, `is_required`, `order`) VALUES
	(1, 'Select frame colour', 1, 0),
	(2, 'Select box colour:', 1, 1);
/*!40000 ALTER TABLE `gc_part_types` ENABLE KEYS */;

-- Дамп данных таблицы bb_dev.gc_user_conf_body: ~9 rows (приблизительно)
DELETE FROM `gc_user_conf_body`;
/*!40000 ALTER TABLE `gc_user_conf_body` DISABLE KEYS */;
INSERT INTO `gc_user_conf_body` (`conf_body_id`, `conf_id`, `external_part_id`) VALUES
	(1, 1, 1),
	(2, 1, 3),
	(3, 2, 1),
	(4, 2, 3),
	(5, 2, 6),
	(6, 3, 1),
	(7, 3, 3),
	(8, 3, 5),
	(9, 3, 6);
/*!40000 ALTER TABLE `gc_user_conf_body` ENABLE KEYS */;

-- Дамп данных таблицы bb_dev.gc_user_conf_head: ~3 rows (приблизительно)
DELETE FROM `gc_user_conf_head`;
/*!40000 ALTER TABLE `gc_user_conf_head` DISABLE KEYS */;
INSERT INTO `gc_user_conf_head` (`conf_id`, `user_id`, `img_id`, `main_part_id`, `parts_count`) VALUES
	(1, 1, NULL, 1, 2),
	(2, 1, NULL, 2, 3),
	(3, 1, NULL, 2, 4);
/*!40000 ALTER TABLE `gc_user_conf_head` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

TRUNCATE TABLE `gc_countries`;
--
-- Dumping data for table `gc_countries`
--

INSERT INTO `gc_countries` (`country_id`, `country_name`) VALUES
(1, 'Ukraine'),
(2, 'Poland'),
(3, 'USA'),
(4, 'Denmark');

--
-- Truncate table before insert `gc_delivery_cost`
--

TRUNCATE TABLE `gc_delivery_cost`;
--
-- Dumping data for table `gc_delivery_cost`
--

INSERT INTO `gc_delivery_cost` (`id`, `country_id`, `delivery_service_id`, `delivery_cost`, `currency`) VALUES
(1, 1, 2, 40.3, 'DKK'),
(2, 3, 3, 80, 'DKK'),
(3, 4, 1, 45.5, 'DKK'),
(4, 1, 5, 62.4, 'DKK');

--
-- Truncate table before insert `gc_delivery_services`
--

TRUNCATE TABLE `gc_delivery_services`;
--
-- Dumping data for table `gc_delivery_services`
--

INSERT INTO `gc_delivery_services` (`ds_id`, `delivery_service_name`) VALUES
(1, 'DHL'),
(2, 'FedEx'),
(3, 'USPS'),
(4, 'Standard intl shipping'),
(5, 'Nova Poshta');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
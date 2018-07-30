SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `base_test`;
CREATE TABLE IF NOT EXISTS `base_test` (`id` int(11) NOT NULL, `uuid` varchar(64) DEFAULT NULL, `order` int(11) DEFAULT 1, `flag` tinyint(1) DEFAULT 0, `createDT` DATETIME NULL DEFAULT NULL, `dt` DATETIME NULL DEFAULT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `base_test` ADD PRIMARY KEY (`id`);
ALTER TABLE `base_test` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
INSERT INTO `base_test` (`uuid`, `order`, `createDT`, `dt`) VALUES ('980e2db4-75af-41a3-946c-5eb22e054241', 1, '2018-07-09 16:41:10', NULL), ('980e2db4-75af-41a3-946c-5eb22e054242', 2, '2018-07-09 16:41:10', NULL), ('980e2db4-75af-41a3-946c-5eb22e054243', 3, '2018-07-09 16:41:10', NULL);
SET FOREIGN_KEY_CHECKS=1;
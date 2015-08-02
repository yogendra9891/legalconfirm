CREATE TABLE IF NOT EXISTS `#__easyquickicons` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `target` varchar(8) NOT NULL,
  `published` tinyint(3) NOT NULL,
  `catid` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `custom_icon` tinyint(1) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `icon_path` varchar(255) NOT NULL,
  `access` int(10) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `checked_out` int(10) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `publish_up` datetime NOT NULL,
  `publish_down` datetime NOT NULL,
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;



INSERT INTO `#__easyquickicons` (`id`, `name`, `link`, `target`, `published`, `catid`, `ordering`, `custom_icon`, `icon`, `icon_path`, `access`, `description`, `created_date`, `modified_date`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `language`) VALUES
("", 'Easy Quickicons', 'index.php?option=com_easyquickicons', '_blank', 1, 0, 0, 1, 'icon-48-clear.png', 'administrator/components/com_easyquickicons/assets/images/icon-48-easyquickicons.png', 1, 'Link to Easy Quickicons Manager', '2013-04-04 08:14:36', '2013-04-15 15:10:36', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '');
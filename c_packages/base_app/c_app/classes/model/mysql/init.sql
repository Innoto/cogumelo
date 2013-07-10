
#
# Administrator User table definition
#
DROP TABLE IF EXISTS `useradmin`;
CREATE TABLE  `useradmin` (
  `id` bigint(20) NOT NULL auto_increment,  
  `login` char(12) collate utf8_spanish_ci default NULL UNIQUE,
  `name` varchar(30) collate utf8_spanish_ci default NULL,
  `passwd` varchar(41) default NULL,
  `time_lastlogin` datetime NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY  USING BTREE (`id`)  	
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Administrator Users Table';

INSERT INTO `useradmin`(`login`,`passwd`, `name`) VALUES ('admin',SHA1('admin'), 'Administrador');



DROP TABLE IF EXISTS `cousa`;
CREATE TABLE  `cousa` (
  `id` bigint(20) NOT NULL auto_increment,  
  `name` varchar(30) default NULL,
  `fingers` int(10) default NULL,
  `hobby` varchar(30) default NULL,
  PRIMARY KEY  USING BTREE (`id`)  	
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='cousa table';

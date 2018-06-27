# Host: localhost  (Version: 5.5.53)
# Date: 2018-06-28 01:06:45
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "yc_auth_group"
#

DROP TABLE IF EXISTS `yc_auth_group`;
CREATE TABLE `yc_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '全新ID',
  `title` char(100) NOT NULL DEFAULT '' COMMENT '标题',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态',
  `rules` longtext COMMENT '规则',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='分组';

#
# Data for table "yc_auth_group"
#

INSERT INTO `yc_auth_group` VALUES (1,'超级管理组',1,'0,275,547,276,277,299,490,491,278,279,288,289,290,552,280,282,283,284,551,553,281,285,286,287,550,558,557,556,555,554,559,562,561,560,',1465114224,1530115928),(14,'普通管理组',1,NULL,1526312087,1526312087),(15,'会员组',1,'278,281,285,286,',1526356659,1526392870),(16,'演示组',1,'0,275,299,490,491,278,279,552,280,551,281,550,541,540,549,542,548,',1527649110,1530106469);

#
# Structure for table "yc_auth_rule"
#

DROP TABLE IF EXISTS `yc_auth_rule`;
CREATE TABLE `yc_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `icon` varchar(50) DEFAULT NULL COMMENT '图标',
  `title` char(50) NOT NULL DEFAULT '' COMMENT '名称',
  `href` char(80) NOT NULL DEFAULT '' COMMENT '控制器/方法',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1校验0不校验',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1启用0禁用',
  `condition` char(100) DEFAULT NULL COMMENT '规则附件条件',
  `pid` int(5) NOT NULL DEFAULT '0' COMMENT '父栏目ID',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `create_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=572 DEFAULT CHARSET=utf8 COMMENT='权限节点';

#
# Data for table "yc_auth_rule"
#

/*!40000 ALTER TABLE `yc_auth_rule` DISABLE KEYS */;
INSERT INTO `yc_auth_rule` VALUES (275,'glyphicon glyphicon-cog','系统设置','admin',1,1,NULL,0,0,NULL,1527057414),(276,'','网站设置','admin/settings/main',1,1,NULL,275,1,NULL,1526717178),(277,'','模板管理','admin/settings/templete',1,1,NULL,275,2,NULL,1526717162),(278,'glyphicon glyphicon-user','权限管理','admin/auth',1,1,NULL,0,3,NULL,1526717151),(279,'','用户组','admin/auth/usergroup',1,1,NULL,278,4,NULL,1526717129),(280,'','权限配置','admin/auth/userrule',1,1,NULL,278,5,NULL,1526717110),(281,'','用户列表','admin/auth/userlist',1,1,NULL,278,6,NULL,1526717100),(282,'','权限添加','admin/auth/ruleadd',1,1,NULL,280,7,NULL,1526717093),(283,'','权限编辑','admin/auth/ruleedit',1,1,NULL,280,8,NULL,1526717086),(284,'','权限删除','admin/auth/ruledel',1,1,NULL,280,9,NULL,1526717077),(285,'','用户添加','admin/auth/useradd',1,1,NULL,281,10,NULL,1526717072),(286,'','用户编辑','admin/auth/useredit',1,1,NULL,281,11,NULL,1526717065),(287,'','用户删除','admin/auth/userdel',1,1,NULL,281,12,NULL,1526717060),(288,'','用户组添加','admin/auth/groupadd',1,1,NULL,279,13,NULL,1526717041),(289,'','用户组编辑','admin/auth/Groupedit',1,1,NULL,279,14,NULL,1526717033),(290,'','用户组删除','admin/auth/groupdel',1,1,NULL,279,15,NULL,1526717025),(299,'','插件列表','admin/plugin/pluginList',1,1,NULL,275,17,1526390526,1526717006),(550,'','用户查看','admin/auth/userlist',1,1,NULL,281,50,1530106342,1530106342),(551,'','权限查看','admin/auth/userrule',1,1,NULL,280,50,1530106404,1530106404),(552,'','用户组查看','admin/auth/usergroup',1,1,NULL,279,50,1530106441,1530106441),(553,'','权限配置','admin/auth/groupconfigrule',1,1,NULL,280,50,1530107331,1530107331);
/*!40000 ALTER TABLE `yc_auth_rule` ENABLE KEYS */;

#
# Structure for table "yc_auth_user"
#

DROP TABLE IF EXISTS `yc_auth_user`;
CREATE TABLE `yc_auth_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `real_name` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `email` varchar(50) DEFAULT NULL COMMENT '邮件',
  `password` char(40) DEFAULT NULL COMMENT '密码',
  `group_id` int(11) DEFAULT NULL COMMENT '分组ID',
  `status` int(11) DEFAULT '1' COMMENT '1启用0禁用',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='用户表';

#
# Data for table "yc_auth_user"
#

/*!40000 ALTER TABLE `yc_auth_user` DISABLE KEYS */;
INSERT INTO `yc_auth_user` VALUES (1,'老杨','admin@admin.com','7c4a8d09ca3762af61e59520943dc26494f8941b',1,1,0,0),(15,'李四','li4@qq.com','7c4a8d09ca3762af61e59520943dc26494f8941b',15,1,1526363785,1526387809),(16,'张三','zhang3@qq.com','7c4a8d09ca3762af61e59520943dc26494f8941b',15,1,1526393447,1526393848),(17,'演示帐号','demo@demo.com','89e495e7941cf9e40e6980d14a16bf023ccd4c91',16,1,1527649261,1527649261);
/*!40000 ALTER TABLE `yc_auth_user` ENABLE KEYS */;

#
# Structure for table "yc_plugin"
#

DROP TABLE IF EXISTS `yc_plugin`;
CREATE TABLE `yc_plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(45) NOT NULL COMMENT '插件名称',
  `description` varchar(45) DEFAULT NULL COMMENT '插件描述',
  `version` varchar(45) NOT NULL COMMENT '插件版本',
  `menu` varchar(1000) NOT NULL COMMENT '插件菜单',
  `dbscript` text COMMENT 'SQL脚本',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `appkey` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=utf8 COMMENT='插件表';

#
# Data for table "yc_plugin"
#

/*!40000 ALTER TABLE `yc_plugin` DISABLE KEYS */;
/*!40000 ALTER TABLE `yc_plugin` ENABLE KEYS */;

#
# Structure for table "yc_site"
#

DROP TABLE IF EXISTS `yc_site`;
CREATE TABLE `yc_site` (
  `id` int(11) NOT NULL COMMENT 'id',
  `sitename` varchar(50) NOT NULL COMMENT '站点名称',
  `keywords` varchar(50) NOT NULL COMMENT '关键字',
  `desc` varchar(200) NOT NULL COMMENT '描述',
  `address` varchar(255) NOT NULL COMMENT '实际地址',
  `email` varchar(255) NOT NULL COMMENT '邮件地址',
  `phone` varchar(15) NOT NULL COMMENT '电话',
  `beian_code` varchar(50) NOT NULL COMMENT '备案号',
  `company` varchar(100) NOT NULL COMMENT '公司名全称',
  `years` varchar(10) DEFAULT NULL COMMENT '年',
  `versions` varchar(20) DEFAULT NULL COMMENT '版本',
  `about_profile` text NOT NULL COMMENT '公司简介',
  `about_us` text NOT NULL COMMENT '公司介绍',
  `is_open` int(11) NOT NULL DEFAULT '1' COMMENT '网站开关，1开启，0关闭',
  `is_guest` int(11) NOT NULL DEFAULT '1' COMMENT '留言开关，1开启，0关闭',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='站点设置';

#
# Data for table "yc_site"
#

/*!40000 ALTER TABLE `yc_site` DISABLE KEYS */;
INSERT INTO `yc_site` VALUES (1,'元诚软件','元诚|软件|元诚软件|访客系统|监控系统|商城系统','元诚|软件|元诚软件|访客系统|监控系统|商城系统','南湖北路121号','netsun@qq.com','13888887777','蜀ICP备17031623号','成都元诚软件有限公司','2018',NULL,'<p>元诚软件有限公司是四川省成都市一家专业从事软件开发、软件定制、软件实施的高新技术企业。拥有一批长期专业从事软件开发、软件定制的专业人才，具有雄厚的技术开发实力，全方位满足政府与企业信息化需求。</p>','<p class=\"MsoNormal\" style=\"text-indent:21.0pt;\">\r\n\t元诚软件有限公司是四川省成都市一家专业从事软件开发、软件定制、软件实施的高新技术企业。拥有一批长期专业从事软件开发、软件定制的专业人才，具有雄厚的技术开发实力，全方位满足政府与企业信息化需求。</p><p class=\"MsoNormal\" style=\"text-indent:21.0pt;\">元诚软件软件经过十多年的经验积累，总结出了针对各行业、不同规模和不同阶段的企业信息化解决方案，我们的项目实施团队能够更加准确快捷地找出客户的具体需求，为您的企业度身定做真正切合实际需求的解决方案。我们实施方面多年的实践积累将为您的企业带来最大投资回报。</p><p class=\"MsoNormal\" style=\"text-indent:21.0pt;\">\r\n\t公司主营业务：软件外包、软件定制开发、系统维护、OA办公系统、手机软件定制等等。</p><p class=\"MsoNormal\" style=\"text-indent:21.0pt;\">\r\n\t元诚软件提供的服务：定制应用开发，实施电子商务网站，移动和无线应用发展，垂直搜索引擎等。</p><p class=\"MsoNormal\" style=\"text-indent:21.0pt;\">\r\n\t元诚软件提供产品：IT综合预警引擎、网站防篡改系统、数据层数据备份系统、数控机床传输平台、4S店管理软件综合平台、大型门户软件、项目综合管理系统、无线生产流程管理系统、外贸订单跟踪管理系统、移动外勤管理平台、政府内外网、行业门户软件、垂直及时搜索引擎等。</p><p class=\"MsoNormal\" style=\"text-indent:21.0pt;\">公司非常重视企业的内部管理工作，市场销售、软件研发、技术支持是公司的三大核心部门，现已经建立了一套比较完善的管理体制。在客户服务方面，本着为客户服务的思想，设立了24小时产品咨询电话、24小时售后技术支持电话等多个无障碍通道，为客户提供了高质量的售前和售后的服务，为元诚软件软件“‘软硬’融合之剑，开辟信息创新之路！”的目标提供了强有力的支持。<br/> </p><p><br/></p>',1,1,0,1529349466);
/*!40000 ALTER TABLE `yc_site` ENABLE KEYS */;

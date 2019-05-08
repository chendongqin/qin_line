# Host: 47.106.95.190  (Version: 5.7.25-0ubuntu0.16.04.2)
# Date: 2019-05-08 14:26:21
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "ql_admin"
#

DROP TABLE IF EXISTS `ql_admin`;
CREATE TABLE `ql_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `nick_name` varchar(45) NOT NULL DEFAULT '' COMMENT '管理员昵称',
  `user_name` varchar(45) NOT NULL DEFAULT '' COMMENT '用户登录名',
  `password` varchar(60) NOT NULL DEFAULT '' COMMENT '密码',
  `login_time` datetime DEFAULT NULL COMMENT '登录时间',
  `group` tinyint(3) NOT NULL DEFAULT '1' COMMENT '管理员群组id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nick_name_UNIQUE` (`nick_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

#
# Data for table "ql_admin"
#

INSERT INTO `ql_admin` VALUES (1,'admin','admin','cf2266b291f63872b75667dbe4db0ab1b4a3fcad:prk8','2019-05-08 15:03:12',1),(3,'lam','13616013855','17afbe203260f3bc6dba69af6865284ec6cd6f99:47ed','2019-05-06 22:52:14',1);

#
# Structure for table "ql_course"
#

DROP TABLE IF EXISTS `ql_course`;
CREATE TABLE `ql_course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_name` varchar(50) NOT NULL DEFAULT '' COMMENT '课程名称',
  `teacher_id` int(11) NOT NULL DEFAULT '0' COMMENT '教师id',
  `long_time` int(3) unsigned NOT NULL DEFAULT '0' COMMENT '第几次上课',
  `begin_date` date DEFAULT NULL COMMENT '课程开始日期',
  `end_date` date DEFAULT NULL COMMENT '课程结束日期',
  `begin_at` char(5) NOT NULL DEFAULT '' COMMENT '上课开始时刻',
  `end_at` char(5) NOT NULL DEFAULT '' COMMENT '课程结束时刻',
  `rooms` varchar(50) NOT NULL DEFAULT '' COMMENT '上课教室（房间 ）',
  `fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '报名费',
  `people` int(3) unsigned NOT NULL DEFAULT '0' COMMENT '上课人数',
  `max_people` int(3) unsigned NOT NULL DEFAULT '100' COMMENT '最大人数限制',
  `isdel` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '软删除标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

#
# Data for table "ql_course"
#

INSERT INTO `ql_course` VALUES (2,'java课程',2,2,'2019-03-17','2019-03-22','9:00','10:00','教室1',500.00,2,100,0),(3,'javascript',2,0,'2019-04-10','2019-04-19','10:00','12:00','room4',100.00,1,100,0),(4,'钢琴8级培训',1,0,'2019-05-16','2019-05-17','09：00','10：00','room3',600.00,1,5,0),(5,'二胡培训',3,0,'2019-05-17','2019-05-18','09:00','10:00','room4',400.00,1,4,0),(6,'html',3,0,'2019-05-25','2019-05-30','','','room5',2222.00,1,10,0);

#
# Structure for table "ql_course_in"
#

DROP TABLE IF EXISTS `ql_course_in`;
CREATE TABLE `ql_course_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT '0' COMMENT '对应用户表id',
  `course_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '课程id',
  `create_at` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

#
# Data for table "ql_course_in"
#

INSERT INTO `ql_course_in` VALUES (4,2,2,20190317160842),(5,3,2,20190317162640),(6,3,3,20190409232905),(10,4,5,20190503212630),(11,4,4,20190503212913),(12,2,6,20190508010047);

#
# Structure for table "ql_course_work"
#

DROP TABLE IF EXISTS `ql_course_work`;
CREATE TABLE `ql_course_work` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `course_id` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '打卡时间Ymd',
  `is_overdue` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否迟到打卡',
  `class_times` int(3) unsigned NOT NULL COMMENT '第几次上课打卡',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Data for table "ql_course_work"
#


#
# Structure for table "ql_goods"
#

DROP TABLE IF EXISTS `ql_goods`;
CREATE TABLE `ql_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_name` varchar(100) NOT NULL DEFAULT '' COMMENT '商品名称',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '价格',
  `describe` varchar(800) NOT NULL DEFAULT '' COMMENT '描述',
  `is_down` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否下架1下架',
  `stock` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '库存量',
  `discount` decimal(5,2) unsigned NOT NULL DEFAULT '100.00' COMMENT '折扣',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

#
# Data for table "ql_goods"
#

INSERT INTO `ql_goods` VALUES (1,'海伦立式钢琴',18999.00,'颜色：黑色亮光  ；型号：120SE；材质：云杉木；背板材质：实木音板；键数：88键',0,4,100.00),(2,'傻狗',6666.00,'测试2',1,10,100.00),(3,'尤克里里',299.00,'颜色：黑色和棕色；型号：W-40；面板材质：云杉木；指板材质：玫瑰木；卷弦器：封闭旋钮；',0,3,100.00),(4,'琵琶乐器',5899.00,'颜色：深棕色；型号：8912-2；面板材质：梧桐木；背板材质：花梨木；弦轴材质：花梨木；',0,3,100.00),(5,'二胡乐器',499.00,'颜色：深棕色；材质：非洲小叶紫檀；琴弦：德国进口钢丝弦；千斤：蚕丝线；弓毛：天然白马尾；',0,5,100.00);

#
# Structure for table "ql_goods_order"
#

DROP TABLE IF EXISTS `ql_goods_order`;
CREATE TABLE `ql_goods_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `goods_id` int(11) NOT NULL DEFAULT '0' COMMENT '商品id',
  `amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '金额',
  `buy_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '购买数量',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态0待支付1已支付2已收货3申请退货4退货成功5退货失败',
  `create_at` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_at` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `address` varchar(300) NOT NULL DEFAULT '' COMMENT '收获地址',
  `discount` decimal(5,2) unsigned NOT NULL DEFAULT '100.00' COMMENT '购买时的折扣',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

#
# Data for table "ql_goods_order"
#

INSERT INTO `ql_goods_order` VALUES (1,2,1,1010.00,1,3,20190411013301,20190425220650,'厦门',100.00),(2,2,1,2020.00,2,3,20190411013516,20190425220413,'泉州',100.00),(3,4,5,499.00,1,1,20190503210201,0,'厦门',100.00),(4,2,3,299.00,1,1,20190507073642,0,'1111',100.00),(5,2,3,299.00,1,1,20190507073704,0,'1111111111',100.00);

#
# Structure for table "ql_teacher"
#

DROP TABLE IF EXISTS `ql_teacher`;
CREATE TABLE `ql_teacher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_no` char(20) NOT NULL DEFAULT '' COMMENT '教师工号',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `password` varchar(60) NOT NULL DEFAULT '' COMMENT '密码',
  `teacher_name` varchar(45) NOT NULL DEFAULT '' COMMENT '教师姓名',
  `birthday` char(10) NOT NULL DEFAULT '' COMMENT '生日',
  `create_at` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_at` bigint(20) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `rank` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0初级教师1中级教师2高级教师3特级教师',
  `salary` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '月薪',
  `isdel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已离职1离职',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  PRIMARY KEY (`id`),
  UNIQUE KEY `teacher_no_UNIQUE` (`teacher_no`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='教师列表';

#
# Data for table "ql_teacher"
#

INSERT INTO `ql_teacher` VALUES (1,'20190317023852','18030016442','73693f166708cbc9aa1487a750a5634a38ec2a94:7m38','t2','1990-03-17',20190317020546,20190503203542,3,3000.00,0,0),(2,'20190317024280','18030016445','f52f3c8057b98c9016a34dd8aabdc28b44fc5736:ynxb','t1','2019-03-18',20190317024638,20190317153028,0,8000.00,0,0),(3,'20190317152599','15980535059','62a7be6409e56d769946a7f507e85be62f5496e4:asdu','嘉伟','1996-01-17',20190317153808,20190505203024,3,9999.00,0,0),(4,'20190503172528','18950182901','90fd2c73df7389b9a41fb8339a4d06ec72ca2df7:aqa6','易老师','1987-03-11',20190503174131,20190503203644,3,8000.00,0,1);

#
# Structure for table "ql_user"
#

DROP TABLE IF EXISTS `ql_user`;
CREATE TABLE `ql_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_no` char(20) NOT NULL DEFAULT '' COMMENT '学号',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `password` varchar(60) NOT NULL DEFAULT '' COMMENT '密码',
  `user_name` varchar(45) NOT NULL DEFAULT '' COMMENT '姓名',
  `birthday` char(10) NOT NULL DEFAULT '' COMMENT '生日',
  `balance` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '账户余额',
  `create_at` bigint(3) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_at` bigint(3) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `isdel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '交易密码',
  `sex` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  PRIMARY KEY (`id`),
  UNIQUE KEY `stu_no_UNIQUE` (`user_no`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='学生列表';

#
# Data for table "ql_user"
#

INSERT INTO `ql_user` VALUES (2,'2019030302000153','18030016446','4db6d2e564b0fb0e853fac86d553089315b214ba:lmvj','zxy','1995-01-02',6632.00,20190303021541,20190508010047,0,0),(3,'2019030314000250','18030016442','fc6bd052c3356f00da393e47985153e052008a73:jskf','user','2019-03-01',7409.00,20190303140410,20190409232905,0,0),(4,'2019030314000314','18850051846','cf2266b291f63872b75667dbe4db0ab1b4a3fcad:prk8','laq','2019-05-06',2707.60,20190303145102,20190506163631,0,1),(5,'2019031716000180','15980535059','34580e36dca39e1f01141c6e456429b6eb27ce1c:xwn0','','',0.00,20190317161229,20190329180902,1,0),(6,'2019050621000195','15860769479','d2e49e43c464ea125494ab30110dacbe14945910:1zp2','','',0.00,20190506212702,20190508152244,1,0),(7,'2019050815000182','13023913188','614e3a9830bb75a4722156ff98ded732cff038d8:feo1','cdq','1995-06-05',500.00,20190508152053,0,0,0),(8,'2019050815000233','15980535982','038c92d903e22dd63866ed629fa36d2837f1487d:yt7r','ysh','2007-02-13',100.00,20190508152406,0,0,1);

#
# Structure for table "ql_work"
#

DROP TABLE IF EXISTS `ql_work`;
CREATE TABLE `ql_work` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '教师id',
  `create_at` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `is_week` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否周末打卡',
  `is_overdue` tinyint(1) unsigned NOT NULL COMMENT '是否迟到打卡',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

#
# Data for table "ql_work"
#

INSERT INTO `ql_work` VALUES (1,1,20190410,0,1),(2,1,20190421,1,1),(3,1,20190425,0,1),(4,3,20190503,0,1),(5,3,20190508,0,1);

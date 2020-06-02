create database e_books;

create table `PHP_admin`(
     `role_id` int(10) unsigned not null primary key comment '用户ID',
     `nickname` varchar(30) not null COMMENT '昵称',
     `password` varchar(50) not null COMMENT '密码',
     `email` varchar(50) not null COMMENT '邮箱',
     `create_time` datetime  default null COMMENT '创建时间',
     `update_time` datetime  default null COMMENT '更新时间',
     `delete_time` datetime  default null COMMENT '删除时间',
     `level` tinyint default '1' comment '管理员等级'
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
insert into PHP_admin(role_id, nickname, password, email,level) values (1,'super_admin','admin123','admin@php.com',0);



create table `PHP_member`(
    `role_id` int(10) unsigned not null primary key comment '用户ID',
    `nickname` varchar(30) not null COMMENT '昵称',
    `password` varchar(50) DEFAULT NULL COMMENT '密码',
    `thumb` varchar(500) default null comment '头像',
    `email` varchar(50) DEFAULT NULL COMMENT '邮箱',
    `sign` text default null comment '个性签名',
    `create_time` datetime  default null COMMENT '创建时间',
    `update_time` datetime  default null COMMENT '更新时间',
    `delete_time` datetime  default null COMMENT '删除时间'
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table `PHP_author`(
     `role_id` int(10) unsigned not null primary key comment '用户ID',
     `nickname` varchar(30) not null COMMENT '笔名',
     `password` varchar(50) DEFAULT NULL COMMENT '密码',
     `thumb` varchar(500) default null comment '头像',
     `email` varchar(50) DEFAULT NULL COMMENT '邮箱',
     `sign` text default null comment '个性签名',
     `create_time` datetime  default null COMMENT '创建时间',
     `update_time` datetime  default null COMMENT '更新时间',
     `delete_time` datetime  default null COMMENT '删除时间'
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table `PHP_category`(
    `category_id` int(10) unsigned not null primary key comment '书籍种类ID',
    `category` varchar(20) not null COMMENT '书籍种类名称',
    `pinyin` varchar(20) not null COMMENT '种类名称拼音'
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10101,'流行小说','novel');
insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10102,'国外小说','guowai');
insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10103,'社科人文','sheke');
insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10104,'国学名著','mingzhu');
insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10105,'儿童读物','ertong');
insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10106,'杂文选集','zawen');
insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10107,'纪实传记','zhuanji');
insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10108,'经济管理','jingji');
insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10109,'外语读本','waiyu');
insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10110,'成功励志','lizhi');
insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10111,'言情小说','yanqing');
insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10112,'科幻小说','kehuan');
insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10113,'惊悚悬疑','jingshu');
insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10114,'武侠小说','wuxia');
insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10115,'侦探推理','zhentan');
insert into `PHP_category`(`category_id`,`category`,`pinyin`)values (10116,'历史小说','lishi');


# create table `PHP_tags`(
#     `tag_id` int(10) unsigned not null primary key auto_increment comment '标签ID',
#     `tag` varchar(20) not null COMMENT '标签'
# )ENGINE=InnoDB DEFAULT CHARSET=utf8;


create table `PHP_article`(
    `article_id` int(10) UNSIGNED NOT NULL auto_increment primary key comment '文章编号',
    `title` varchar(150) NOT NULL comment '书籍名称',
    `category_id` int(10) UNSIGNED NOT NULL comment '分类编号',
    `author_id` varchar(10) not null COMMENT '作者ID',
    `summary` varchar(500) DEFAULT NULL COMMENT '简介',
    `thumb` varchar(255) DEFAULT NULL COMMENT '封面/缩略图',
    `create_time` datetime NOT NULL  COMMENT '发表时间',
    `update_time` datetime NOT NULL COMMENT '更新时间',
    `delete_time` datetime default null COMMENT '删除时间',
    `tags` varchar(255) DEFAULT NULL COMMENT '标签相关',
    `last_chapter_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最新章节ID',
    `push` tinyint( 1 ) NOT NULL DEFAULT  '0' COMMENT '推送标记'
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table `PHP_chapter`(
    `chapter_url` int(10) UNSIGNED NOT NULL primary key comment '章节编号',
    `chapter_id` int(5) unsigned not null unique comment '章节数',
    `article_id` int(10) UNSIGNED NOT NULL comment '文章编号',
    `title` varchar(50) not null comment '章节主题',
    `content` text not null comment '章节内容',
    `create_time` datetime NOT NULL  COMMENT '发表时间',
    `update_time` datetime NOT NULL COMMENT '更新时间',
    `delete_time` datetime default null COMMENT '删除时间'
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table `PHP_comment`(
    `comment_id` int(10) UNSIGNED NOT NULL primary key auto_increment comment '评论编号',
    `article_id` int(10) UNSIGNED NOT NULL comment '文章编号',
    `content` text not null comment '评论内容',
    `user_id` int(10) unsigned not null comment '用户ID',
    `reply_id` int(10) unsigned not null default '0' comment '楼主评论ID',
    `create_time` datetime NOT NULL  COMMENT '发表时间',
    `update_time` datetime NOT NULL COMMENT '更新时间',
    `delete_time` datetime default null COMMENT '删除时间'
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table `PHP_fav`(
    `article_id` int(10) UNSIGNED NOT NULL primary key comment '文章编号',
    `views` int(10) UNSIGNED default 0 comment '点击量',
    `favorite` int(10) UNSIGNED default 0 comment '收藏量',
    `create_time` datetime NOT NULL  COMMENT '发表时间',
    `update_time` datetime NOT NULL COMMENT '更新时间',
    `delete_time` datetime default null COMMENT '删除时间'
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table `PHP_bookshelf`(
    `member_id` int(10) UNSIGNED NOT NULL comment '会员号',
    `article_id` int(10) UNSIGNED NOT NULL  comment '文章编号',
    `chapter_url` int(10) unsigned default 0 comment '上次观看',
    `create_time` datetime NOT NULL  COMMENT '发表时间',
    `update_time` datetime NOT NULL COMMENT '更新时间',
    `delete_time` datetime default null COMMENT '删除时间'
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

# create table `PHP_records`(
#     `member_id` int(10) UNSIGNED NOT NULL primary key comment '会员号',
#     `article_id` int(10) UNSIGNED NOT NULL  comment '文章编号',
#     `chapter_id` int(10) UNSIGNED NOT NULL  comment '章节编号',
#     `last` datetime comment '上次观看结束时间'
# )ENGINE=InnoDB DEFAULT CHARSET=utf8;

# create table `PHP_group`(
#     `group_id` int(10) UNSIGNED NOT NULL primary key auto_increment comment '组ID',
#     `member` varchar(500) not null comment '组成员',
#     `number`int(10) unsigned not null comment '成员数量'
# )ENGINE=InnoDB DEFAULT CHARSET=utf8;

create table `PHP_sys_info`(
    `info_id` int(10) UNSIGNED NOT NULL primary key auto_increment comment '通知编号',
    `sender`  int(10) unsigned not null comment '管理员ID',
    `recipient` int(10) unsigned not null comment '用户ID/组ID',
    `title` varchar(255) not null comment '通知主题',
    `content` text not null comment '评论内容',
    `read` tinyint not null default 0 comment '已读',
    `create_time` datetime  comment '发布时间',
    `update_time` datetime NOT NULL COMMENT '更新时间',
    `delete_time` datetime default null COMMENT '删除时间'
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

# create table `PHP_user_info`(
#     `info_id` int(10) UNSIGNED NOT NULL primary key auto_increment comment '消息编号',
#     `sender` int(10) UNSIGNED NOT NULL comment '发送者',
#     `recipient` int(10) UNSIGNED NOT NULL comment '接收者',
#     `title` varchar(255) not null comment '主题',
#     `content` varchar(500) not null comment '内容',
#     `read` tinyint not null default 0 comment '已读',
#     `create_time` datetime  comment '发送时间',
#     `update_time` datetime NOT NULL COMMENT '更新时间',
#     `delete_time` datetime default null COMMENT '删除时间'
# )ENGINE=InnoDB DEFAULT CHARSET=utf8;

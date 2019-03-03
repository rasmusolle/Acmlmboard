-- Adminer 4.7.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP DATABASE IF EXISTS `acmlmboard`;
CREATE DATABASE `acmlmboard` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `acmlmboard`;

DROP TABLE IF EXISTS `blockedlayouts`;
CREATE TABLE `blockedlayouts` (
  `user` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `blockee` mediumint(8) unsigned NOT NULL DEFAULT '0',
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `ord` tinyint(4) NOT NULL,
  `minpower` tinyint(4) NOT NULL,
  `private` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `categories` (`id`, `title`, `ord`, `minpower`, `private`) VALUES
(1,	'General',	2,	0,	0),
(2,	'Staff Forums',	0,	1,	1);

DROP TABLE IF EXISTS `forums`;
CREATE TABLE `forums` (
  `id` int(5) NOT NULL DEFAULT '0',
  `cat` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ord` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `descr` varchar(255) NOT NULL,
  `threads` mediumint(8) NOT NULL DEFAULT '0',
  `posts` mediumint(8) NOT NULL DEFAULT '0',
  `lastdate` int(11) NOT NULL DEFAULT '0',
  `lastuser` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `lastid` int(11) NOT NULL,
  `private` int(1) NOT NULL,
  `readonly` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `forums` (`id`, `cat`, `ord`, `title`, `descr`, `threads`, `posts`, `lastdate`, `lastuser`, `lastid`, `private`, `readonly`) VALUES
(1,	1,	1,	'General Forum',	'General topics forum',	0,	0,	0,	0,	0,	0,	0),
(2,	2,	1,	'General Staff Forum',	'Generic Staff Forum',	0,	0,	0,	0,	0,	1,	0);

DROP TABLE IF EXISTS `forumsread`;
CREATE TABLE `forumsread` (
  `uid` mediumint(9) NOT NULL,
  `fid` int(5) NOT NULL,
  `time` int(11) NOT NULL,
  UNIQUE KEY `uid` (`uid`,`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `group`;
CREATE TABLE `group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `nc` varchar(6) NOT NULL,
  `inherit_group_id` int(11) NOT NULL,
  `default` int(2) NOT NULL,
  `banned` int(2) NOT NULL,
  `sortorder` int(11) NOT NULL DEFAULT '0',
  `visible` int(1) NOT NULL DEFAULT '0',
  `primary` int(1) NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

INSERT INTO `group` (`id`, `title`, `nc`, `inherit_group_id`, `default`, `banned`, `sortorder`, `visible`, `primary`, `description`) VALUES
(1,	'Base User',	'',	0,	0,	0,	100,	0,	0,	''),
(2,	'Normal User',	'4f77ff',	1,	1,	0,	200,	1,	1,	'Normal Registered User'),
(3,	'Moderator',	'47B53C',	10,	0,	0,	600,	1,	1,	''),
(4,	'Administrator',	'd8b00d',	3,	0,	0,	700,	1,	1,	''),
(6,	'Root Administrator',	'AA3C3C',	0,	-1,	0,	800,	1,	1,	''),
(9,	'Banned',	'888888',	2,	0,	1,	0,	1,	1,	''),
(10,	'Staff',	'',	2,	0,	0,	300,	0,	0,	''),
(15,	'Bot',	'',	1,	0,	0,	50,	0,	0,	'');

DROP TABLE IF EXISTS `guests`;
CREATE TABLE `guests` (
  `date` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ipbanned` tinyint(4) NOT NULL DEFAULT '0',
  `useragent` varchar(255) NOT NULL,
  `bot` int(11) NOT NULL,
  `lastforum` int(10) NOT NULL,
  UNIQUE KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `ipbans`;
CREATE TABLE `ipbans` (
  `ipmask` varchar(15) NOT NULL,
  `hard` tinyint(1) NOT NULL,
  `expires` int(12) NOT NULL,
  `banner` varchar(25) NOT NULL,
  `reason` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `t` int(12) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `uid` int(11) NOT NULL,
  `request` varchar(255) NOT NULL,
  KEY `t` (`t`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `misc`;
CREATE TABLE `misc` (
  `field` varchar(255) NOT NULL,
  `intval` int(11) NOT NULL DEFAULT '0',
  `txtval` text NOT NULL,
  PRIMARY KEY (`field`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `misc` (`field`, `intval`, `txtval`) VALUES
('views',	0,	''),
('maxpostsday',	0,	''),
('maxpostsdaydate',	0,	''),
('maxpostshour',	0,	''),
('maxpostshourdate',	0,	''),
('maxusers',	0,	''),
('maxusersdate',	0,	''),
('maxuserstext',	0,	''),
('botviews',	0,	''),
('attention',	0,	'');

DROP TABLE IF EXISTS `perm`;
CREATE TABLE `perm` (
  `id` varchar(64) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `permcat_id` int(11) NOT NULL,
  `permbind_id` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES
('assign-secondary-groups',	'Assign Secondary Groups',	'',	3,	''),
('ban-users',	'Ban Users',	'',	3,	''),
('banned',	'Is Banned',	'',	2,	''),
('block-layout',	'Enable Layout Blocking',	'Enables per-user layout blocking',	3,	''),
('bypass-lockdown',	'View Board Under Lockdown',	'',	3,	''),
('can-edit-group',	'Edit Group Assets',	'',	3,	'group'),
('can-edit-group-member',	'Edit User Assets',	'',	3,	'group'),
('consecutive-posts',	'Consecutive Posts',	'',	2,	''),
('create-all-private-forum-posts',	'Create All Private Forum Posts',	'',	3,	''),
('create-all-private-forum-threads',	'Create All Private Forum Threads',	'',	3,	''),
('create-forum-announcements',	'Create Forum Announcements',	'',	4,	''),
('create-pms',	'Create PMs',	'',	1,	''),
('create-private-forum-post',	'Create Private Forum Post',	'',	2,	'forums'),
('create-private-forum-thread',	'Create Private Forum Thread',	'',	2,	'forums'),
('create-public-post',	'Create Public Post',	'',	4,	''),
('create-public-thread',	'Create Public Thread',	'',	4,	''),
('delete-forum-post',	'Delete Forum Post',	'',	2,	'forums'),
('delete-forum-thread',	'Delete Forum Thread',	'',	2,	'forums'),
('delete-own-pms',	'Delete Own PMs',	'',	1,	''),
('delete-post',	'Delete Post',	'',	2,	''),
('delete-thread',	'Delete Thread',	'',	2,	''),
('delete-user-pms',	'Delete User PMs',	'',	3,	''),
('edit-all-group',	'Edit All Group Assets',	'',	3,	''),
('edit-all-group-member',	'Edit All User Assets',	'',	3,	''),
('edit-attentions-box',	'Edit Attentions Box',	'',	3,	''),
('edit-categories',	'Edit Categories',	'',	3,	''),
('edit-customusercolors',	'Edit Custom Username Colors',	'',	3,	''),
('edit-displaynames',	'Edit Displaynames',	'',	3,	''),
('edit-forum-post',	'Edit Forum Post',	'',	2,	'forums'),
('edit-forum-thread',	'Edit Forum Thread',	'',	2,	'forums'),
('edit-forums',	'Edit Forums',	'',	3,	''),
('edit-groups',	'Edit Groups',	'',	3,	''),
('edit-ip-bans',	'Edit IP Bans',	'',	0,	''),
('edit-own-permissions',	'Edit Own Permissions',	'',	3,	''),
('edit-own-title',	'Edit Own Title',	'',	3,	''),
('edit-permissions',	'Edit Permissions',	'',	3,	''),
('edit-titles',	'Edit Titles',	'',	3,	''),
('edit-user-customnickcolor',	'Edit User Custom Nick Color',	'',	3,	'users'),
('edit-user-displayname',	'Edit User Displayname',	'',	3,	'users'),
('edit-users',	'Edit Users',	'',	3,	''),
('has-customusercolor',	'Can Edit Custom Username Color',	'',	3,	''),
('has-displayname',	'Can Use Displayname',	'',	3,	''),
('ignore-thread-time-limit',	'Ignore Thread Time Limit',	'',	0,	''),
('manage-board',	'Administration Management Panel',	'',	3,	''),
('no-restrictions',	'No Restrictions',	'',	3,	''),
('override-closed',	'Post in Closed Threads',	'',	2,	''),
('override-readonly-forums',	'Override Read Only Forums',	'',	3,	''),
('post-offline',	'Post Offline',	'',	4,	''),
('rename-own-thread',	'Rename Own Thread',	'',	1,	''),
('show-as-staff',	'Listed Publicly as Staff',	'',	3,	'users'),
('staff',	'Is Staff',	'',	2,	''),
('track-ip-change',	'Track IP Changes in IRC',	'Add this to a group or user to have their IP change reported to the staff channel.',	3,	''),
('update-own-post',	'Update Own Post',	'',	4,	''),
('update-own-profile',	'Update Own Profile',	'',	1,	''),
('update-post',	'Update Post',	'',	2,	''),
('update-profiles',	'Update Profiles',	'',	3,	''),
('update-thread',	'Update Thread',	'',	2,	''),
('update-user-profile',	'Update User Profile',	'',	3,	'users'),
('use-post-layout',	'Use Post Layout',	'',	4,	''),
('view-all-private-categories',	'View All Private Categories',	'',	3,	''),
('view-all-private-forums',	'View All Private Forums',	'',	3,	''),
('view-all-private-posts',	'View All Private Posts',	'',	3,	''),
('view-all-private-threads',	'View All Private Threads',	'',	3,	''),
('view-own-pms',	'View Own PMs',	'',	1,	''),
('view-permissions',	'View Permissions',	'',	3,	''),
('view-post-history',	'View Post History',	'',	2,	''),
('view-post-ips',	'View Post IP Addresses',	'',	3,	''),
('view-private-category',	'View Private Category',	'',	2,	'categories'),
('view-private-forum',	'View Private Forum',	'',	2,	'forums'),
('view-private-post',	'View Private Post',	'',	2,	'posts'),
('view-private-thread',	'View Private Thread',	'',	2,	'threads'),
('view-profile-page',	'View Profile Page',	'',	1,	''),
('view-public-categories',	'View Public Categories',	'',	1,	''),
('view-public-forums',	'View Public Forums',	'',	1,	''),
('view-public-posts',	'View Public Posts',	'',	1,	''),
('view-public-threads',	'View Public Threads',	'',	1,	''),
('view-user-pms',	'View User PMs',	'',	3,	''),
('view-user-urls',	'View User URLs',	'',	3,	'');

DROP TABLE IF EXISTS `permbind`;
CREATE TABLE `permbind` (
  `id` varchar(64) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `permbind` (`id`, `title`) VALUES
('categories',	'Category'),
('forums',	'Forum'),
('group',	'Group'),
('posts',	'Post'),
('threads',	'Thread'),
('users',	'User');

DROP TABLE IF EXISTS `permcat`;
CREATE TABLE `permcat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `sortorder` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `permcat` (`id`, `title`, `sortorder`) VALUES
(1,	'Basic',	100),
(2,	'Moderator',	200),
(3,	'Administrative',	300),
(4,	'Posting',	150);

DROP TABLE IF EXISTS `perm_permbind`;
CREATE TABLE `perm_permbind` (
  `perm_id` varchar(64) NOT NULL,
  `permbind_id` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `perm_permbind` (`perm_id`, `permbind_id`) VALUES
('view-private-category',	'categories'),
('view-private-forum',	'forums'),
('view-private-thread',	'threads'),
('view-private-post',	'posts');

DROP TABLE IF EXISTS `pmsgs`;
CREATE TABLE `pmsgs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL DEFAULT '0',
  `nolayout` int(1) NOT NULL,
  `ip` char(15) NOT NULL,
  `userto` mediumint(9) unsigned NOT NULL,
  `userfrom` mediumint(9) unsigned NOT NULL,
  `unread` tinyint(4) NOT NULL,
  `del_from` tinyint(1) NOT NULL DEFAULT '0',
  `del_to` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `pmsgstext`;
CREATE TABLE `pmsgstext` (
  `id` int(11) unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `polloptions`;
CREATE TABLE `polloptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll` int(11) NOT NULL,
  `option` varchar(255) NOT NULL,
  `r` smallint(3) NOT NULL,
  `g` smallint(3) NOT NULL,
  `b` smallint(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `poll` (`poll`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `polls`;
CREATE TABLE `polls` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `multivote` int(1) NOT NULL DEFAULT '0',
  `changeable` int(1) NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `pollvotes`;
CREATE TABLE `pollvotes` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  UNIQUE KEY `id_2` (`id`,`user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `thread` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL DEFAULT '0',
  `nolayout` int(1) NOT NULL,
  `ip` char(15) NOT NULL,
  `num` mediumint(9) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `announce` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `threadid` (`thread`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `poststext`;
CREATE TABLE `poststext` (
  `id` int(11) unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `revision` int(5) NOT NULL DEFAULT '1',
  `date` int(11) NOT NULL,
  `user` mediumint(9) NOT NULL,
  PRIMARY KEY (`id`,`revision`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `threads`;
CREATE TABLE `threads` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `replies` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `views` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `closed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sticky` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `forum` int(5) NOT NULL DEFAULT '0',
  `user` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `lastdate` int(11) NOT NULL DEFAULT '0',
  `lastuser` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `lastid` int(11) NOT NULL DEFAULT '0',
  `announce` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `threadsread`;
CREATE TABLE `threadsread` (
  `uid` mediumint(9) NOT NULL,
  `tid` mediumint(9) NOT NULL,
  `time` int(11) NOT NULL,
  UNIQUE KEY `uid` (`uid`,`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `displayname` varchar(32) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `posts` mediumint(9) NOT NULL DEFAULT '0',
  `threads` mediumint(9) NOT NULL DEFAULT '0',
  `regdate` int(11) NOT NULL DEFAULT '0',
  `lastpost` int(11) NOT NULL DEFAULT '0',
  `lastview` int(11) NOT NULL DEFAULT '0',
  `lastforum` int(10) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `ipfwd` varchar(64) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ipbanned` tinyint(4) NOT NULL DEFAULT '0',
  `tempbanned` int(12) NOT NULL,
  `canreport` tinyint(4) NOT NULL DEFAULT '1',
  `sex` tinyint(4) NOT NULL DEFAULT '2',
  `dateformat` varchar(15) NOT NULL DEFAULT 'Y-m-d',
  `timeformat` varchar(15) NOT NULL DEFAULT 'H:i',
  `ppp` smallint(3) unsigned NOT NULL DEFAULT '20',
  `tpp` smallint(3) unsigned NOT NULL DEFAULT '20',
  `longpages` int(1) NOT NULL DEFAULT '0',
  `fontsize` smallint(5) unsigned NOT NULL DEFAULT '68',
  `theme` varchar(32) NOT NULL DEFAULT 'bmatrix',
  `birth` varchar(10) NOT NULL DEFAULT '-1',
  `rankset` int(10) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `realname` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `homeurl` varchar(255) NOT NULL,
  `homename` varchar(255) NOT NULL,
  `usepic` tinyint(4) NOT NULL DEFAULT '0',
  `head` text NOT NULL,
  `sign` text NOT NULL,
  `signsep` int(1) NOT NULL DEFAULT '0',
  `bio` text NOT NULL,
  `etc` int(11) NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '1',
  `nick_color` varchar(6) NOT NULL,
  `enablecolor` int(1) NOT NULL DEFAULT '0',
  `blocklayouts` int(11) NOT NULL DEFAULT '0',
  `timezone` varchar(128) NOT NULL DEFAULT 'UTC',
  `emailhide` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `user_group`;
CREATE TABLE `user_group` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `sortorder` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user_group` (`user_id`, `group_id`, `sortorder`) VALUES
(0,	1,	0);

DROP TABLE IF EXISTS `x_perm`;
CREATE TABLE `x_perm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `x_id` int(11) NOT NULL,
  `x_type` varchar(64) NOT NULL,
  `perm_id` varchar(64) NOT NULL,
  `permbind_id` varchar(64) NOT NULL,
  `bindvalue` int(11) NOT NULL,
  `revoke` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8;

INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES
(1,	2,	'group',	'update-own-profile',	'',	0,	0),
(2,	1,	'group',	'view-profile-page',	'',	0,	0),
(3,	1,	'group',	'view-public-categories',	'',	0,	0),
(4,	1,	'group',	'view-public-forums',	'',	0,	0),
(5,	1,	'group',	'view-public-posts',	'',	0,	0),
(6,	1,	'group',	'view-public-threads',	'',	0,	0),
(7,	2,	'group',	'create-public-post',	'',	0,	0),
(8,	2,	'group',	'create-public-thread',	'',	0,	0),
(9,	2,	'group',	'update-own-post',	'',	0,	0),
(10,	2,	'group',	'use-post-layout',	'',	0,	0),
(11,	3,	'group',	'delete-post',	'',	0,	0),
(12,	3,	'group',	'delete-thread',	'',	0,	0),
(13,	3,	'group',	'update-post',	'',	0,	0),
(14,	3,	'group',	'update-thread',	'',	0,	0),
(15,	3,	'group',	'view-post-history',	'',	0,	0),
(16,	4,	'group',	'edit-attentions-box',	'',	0,	0),
(17,	4,	'group',	'edit-categories',	'',	0,	0),
(18,	4,	'group',	'edit-forums',	'',	0,	0),
(19,	4,	'group',	'edit-permissions',	'',	0,	0),
(20,	4,	'group',	'view-all-private-categories',	'',	0,	0),
(21,	4,	'group',	'view-all-private-forums',	'',	0,	0),
(22,	4,	'group',	'view-all-private-posts',	'',	0,	0),
(23,	4,	'group',	'view-all-private-threads',	'',	0,	0),
(24,	4,	'group',	'view-permissions',	'',	0,	0),
(25,	6,	'group',	'no-restrictions',	'',	0,	0),
(26,	4,	'group',	'create-all-private-forum-threads',	'',	0,	0),
(27,	4,	'group',	'create-all-private-forum-posts',	'',	0,	0),
(28,	10,	'group',	'view-private-category',	'categories',	2,	0),
(29,	9,	'group',	'create-public-thread',	'',	0,	1),
(30,	9,	'group',	'create-public-post',	'',	0,	1),
(31,	9,	'group',	'update-own-post',	'',	0,	1),
(32,	9,	'group',	'update-own-profile',	'',	0,	1),
(33,	4,	'group',	'update-profiles',	'',	0,	0),
(34,	9,	'group',	'banned',	'',	0,	0),
(35,	2,	'group',	'rename-own-thread',	'',	0,	0),
(36,	4,	'group',	'view-post-ips',	'',	0,	0),
(37,	2,	'group',	'view-user-urls',	'',	0,	0),
(39,	4,	'group',	'edit-users',	'',	0,	0),
(40,	2,	'group',	'create-pms',	'',	0,	0),
(41,	2,	'group',	'delete-own-pms',	'',	0,	0),
(42,	2,	'group',	'view-own-pms',	'',	0,	0),
(46,	4,	'group',	'override-readonly-forums',	'',	0,	0),
(47,	9,	'group',	'rename-own-thread',	'',	0,	1),
(48,	4,	'group',	'edit-ip-bans',	'',	0,	0),
(49,	3,	'group',	'create-forum-announcements',	'',	0,	0),
(50,	5,	'group',	'view-private-forum',	'',	21,	0),
(51,	5,	'group',	'create-private-forum-post',	'',	21,	0),
(52,	5,	'group',	'create-private-forum-thread',	'',	21,	0),
(53,	10,	'group',	'has-displayname',	'',	0,	0),
(55,	3,	'group',	'show-as-staff',	'',	0,	0),
(56,	4,	'group',	'show-as-staff',	'',	0,	0),
(57,	10,	'group',	'track-ip-change',	'',	0,	0),
(58,	2,	'group',	'block-layout',	'',	0,	0),
(59,	9,	'group',	'edit-own-title',	'',	0,	1),
(60,	3,	'group',	'override-closed',	'',	0,	0),
(63,	4,	'group',	'manage-board',	'',	0,	0),
(64,	4,	'group',	'edit-all-group',	'',	0,	0),
(65,	1,	'group',	'create-public-thread',	'',	0,	0),
(66,	3,	'group',	'ban-users',	'',	0,	0),
(67,	4,	'group',	'has-customusercolor',	'',	0,	0),
(70,	1,	'group',	'create-private-forum-post',	'forums',	2,	1),
(71,	1,	'group',	'create-private-forum-thread',	'forums',	2,	1),
(72,	1,	'group',	'delete-forum-post',	'forums',	2,	1),
(73,	1,	'group',	'delete-forum-thread',	'forums',	2,	1),
(74,	1,	'group',	'edit-forum-post',	'forums',	2,	1),
(75,	1,	'group',	'edit-forum-thread',	'forums',	2,	1),
(78,	1,	'group',	'view-private-forum',	'forums',	2,	1),
(81,	10,	'group',	'create-private-forum-post',	'forums',	2,	0),
(82,	10,	'group',	'create-private-forum-thread',	'forums',	2,	0),
(83,	10,	'group',	'delete-forum-post',	'forums',	2,	0),
(84,	10,	'group',	'delete-forum-thread',	'forums',	2,	0),
(85,	10,	'group',	'edit-forum-post',	'forums',	2,	0),
(86,	10,	'group',	'edit-forum-thread',	'forums',	2,	0),
(89,	10,	'group',	'view-private-forum',	'forums',	2,	0),
(103,	1,	'group',	'create-private-forum-post',	'forums',	1,	1),
(104,	1,	'group',	'create-private-forum-thread',	'forums',	1,	1),
(105,	1,	'group',	'delete-forum-post',	'forums',	1,	1),
(106,	1,	'group',	'delete-forum-thread',	'forums',	1,	1),
(107,	1,	'group',	'edit-forum-post',	'forums',	1,	1),
(108,	1,	'group',	'edit-forum-thread',	'forums',	1,	1),
(111,	1,	'group',	'view-private-forum',	'forums',	1,	1),
(123,	1,	'group',	'view-private-category',	'categories',	1,	1),
(125,	4,	'group',	'edit-titles',	'',	0,	0),
(126,	6,	'group',	'show-as-staff',	'',	0,	0);

-- 2019-02-22 16:11:07

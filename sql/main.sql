-- Adminer 4.7.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `categories` (`id`, `title`, `ord`) VALUES
(1,	'General',	2),
(2,	'Staff Forums',	0);

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


DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `nc` varchar(6) NOT NULL,
  `inherit_group_id` int(11) NOT NULL,
  `default` int(2) NOT NULL,
  `banned` int(2) NOT NULL,
  `sortorder` int(11) NOT NULL DEFAULT '0',
  `visible` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

INSERT INTO `groups` (`id`, `title`, `nc`, `inherit_group_id`, `default`, `banned`, `sortorder`, `visible`) VALUES
(1,	'Banned',	'888888',	3,	0,	1,	0,	1),
(2,	'Base User',	'',	0,	0,	0,	100,	0),
(3,	'Normal User',	'4f77ff',	2,	1,	0,	200,	1),
(4,	'Staff',	'',	3,	0,	0,	300,	0),
(5,	'Moderator',	'47B53C',	4,	0,	0,	600,	1),
(6,	'Administrator',	'd8b00d',	5,	0,	0,	700,	1),
(7,	'Root Administrator',	'AA3C3C',	0,	-1,	0,	800,	1);

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


DROP TABLE IF EXISTS `misc`;
CREATE TABLE `misc` (
  `field` varchar(255) NOT NULL,
  `intval` int(11) NOT NULL DEFAULT '0',
  `txtval` text NOT NULL,
  PRIMARY KEY (`field`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `misc` (`field`, `intval`, `txtval`) VALUES
('views',	0,	''),
('botviews',	0,	''),
('attention',	0,	'');

DROP TABLE IF EXISTS `perm`;
CREATE TABLE `perm` (
  `id` varchar(64) NOT NULL,
  `title` varchar(255) NOT NULL,
  `permcat_id` int(11) NOT NULL,
  `permbind_id` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `perm` (`id`, `title`, `permcat_id`, `permbind_id`) VALUES
('ban-users',	'Ban Users',	3,	''),
('bypass-lockdown',	'View Board Under Lockdown',	3,	''),
('can-edit-group',	'Edit Group Assets',	3,	'group'),
('can-edit-group-member',	'Edit User Assets',	3,	'group'),
('consecutive-posts',	'Consecutive Posts',	2,	''),
('create-all-private-forum-posts',	'Create All Private Forum Posts',	3,	''),
('create-all-private-forum-threads',	'Create All Private Forum Threads',	3,	''),
('create-forum-announcements',	'Create Forum Announcements',	4,	''),
('create-pms',	'Create PMs',	1,	''),
('create-private-forum-post',	'Create Private Forum Post',	2,	'forums'),
('create-private-forum-thread',	'Create Private Forum Thread',	2,	'forums'),
('create-public-post',	'Create Public Post',	4,	''),
('create-public-thread',	'Create Public Thread',	4,	''),
('delete-forum-post',	'Delete Forum Post',	2,	'forums'),
('delete-forum-thread',	'Delete Forum Thread',	2,	'forums'),
('delete-own-pms',	'Delete Own PMs',	1,	''),
('delete-post',	'Delete Post',	2,	''),
('delete-thread',	'Delete Thread',	2,	''),
('delete-user-pms',	'Delete User PMs',	3,	''),
('edit-all-group',	'Edit All Group Assets',	3,	''),
('edit-all-group-member',	'Edit All User Assets',	3,	''),
('edit-attentions-box',	'Edit Attentions Box',	3,	''),
('edit-customusercolors',	'Edit Custom Username Colors',	3,	''),
('edit-displaynames',	'Edit Displaynames',	3,	''),
('edit-forum-post',	'Edit Forum Post',	2,	'forums'),
('edit-forum-thread',	'Edit Forum Thread',	2,	'forums'),
('edit-forums',	'Edit Forums',	3,	''),
('edit-groups',	'Edit Groups',	3,	''),
('edit-ip-bans',	'Edit IP Bans',	0,	''),
('edit-own-permissions',	'Edit Own Permissions',	3,	''),
('edit-own-title',	'Edit Own Title',	3,	''),
('edit-permissions',	'Edit Permissions',	3,	''),
('edit-titles',	'Edit Titles',	3,	''),
('edit-users',	'Edit Users',	3,	''),
('has-customusercolor',	'Can Edit Custom Username Color',	3,	''),
('has-displayname',	'Can Use Displayname',	3,	''),
('ignore-thread-time-limit',	'Ignore Thread Time Limit',	0,	''),
('manage-board',	'Administration Management Panel',	3,	''),
('no-restrictions',	'No Restrictions',	3,	''),
('override-closed',	'Post in Closed Threads',	2,	''),
('override-readonly-forums',	'Override Read Only Forums',	3,	''),
('rename-own-thread',	'Rename Own Thread',	1,	''),
('update-own-post',	'Update Own Post',	4,	''),
('update-own-profile',	'Update Own Profile',	1,	''),
('update-post',	'Update Post',	2,	''),
('update-profiles',	'Update Profiles',	3,	''),
('update-thread',	'Update Thread',	2,	''),
('use-post-layout',	'Use Post Layout',	4,	''),
('view-all-private-forums',	'View All Private Forums',	3,	''),
('view-own-pms',	'View Own PMs',	1,	''),
('view-post-history',	'View Post History',	2,	''),
('view-post-ips',	'View Post IP Addresses',	3,	''),
('view-private-forum',	'View Private Forum',	2,	'forums'),
('view-user-pms',	'View User PMs',	3,	'');

DROP TABLE IF EXISTS `permbind`;
CREATE TABLE `permbind` (
  `id` varchar(64) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `permbind` (`id`, `title`) VALUES
('forums',	'Forum'),
('group',	'Group'),
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

DROP TABLE IF EXISTS `pmsgs`;
CREATE TABLE `pmsgs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL DEFAULT '0',
  `ip` char(15) NOT NULL,
  `userto` mediumint(9) unsigned NOT NULL,
  `userfrom` mediumint(9) unsigned NOT NULL,
  `unread` tinyint(4) NOT NULL DEFAULT '1',
  `del_from` tinyint(1) NOT NULL DEFAULT '0',
  `del_to` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `thread` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL DEFAULT '0',
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
  `gender` tinyint(4) NOT NULL DEFAULT '2',
  `dateformat` varchar(15) NOT NULL DEFAULT 'Y-m-d',
  `timeformat` varchar(15) NOT NULL DEFAULT 'H:i',
  `ppp` smallint(3) unsigned NOT NULL DEFAULT '20',
  `tpp` smallint(3) unsigned NOT NULL DEFAULT '20',
  `theme` varchar(32) NOT NULL DEFAULT 'bmatrix',
  `birth` varchar(10) NOT NULL DEFAULT '-1',
  `rankset` int(10) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `usepic` tinyint(4) NOT NULL DEFAULT '0',
  `head` text NOT NULL,
  `sign` text NOT NULL,
  `signsep` int(1) NOT NULL DEFAULT '0',
  `bio` text NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '1',
  `nick_color` varchar(6) NOT NULL,
  `enablecolor` int(1) NOT NULL DEFAULT '0',
  `blocklayouts` int(11) NOT NULL DEFAULT '0',
  `timezone` varchar(128) NOT NULL DEFAULT 'UTC',
  `emailhide` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `x_perm` (`x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES
(1,	'group',	'create-public-post',	'',	0,	1),
(1,	'group',	'create-public-thread',	'',	0,	1),
(1,	'group',	'edit-own-title',	'',	0,	1),
(1,	'group',	'rename-own-thread',	'',	0,	1),
(1,	'group',	'update-own-post',	'',	0,	1),
(1,	'group',	'update-own-profile',	'',	0,	1),
(2,	'group',	'create-private-forum-post',	'forums',	1,	1),
(2,	'group',	'create-private-forum-post',	'forums',	2,	1),
(2,	'group',	'create-private-forum-thread',	'forums',	1,	1),
(2,	'group',	'create-private-forum-thread',	'forums',	2,	1),
(2,	'group',	'create-public-thread',	'',	0,	0),
(2,	'group',	'delete-forum-post',	'forums',	1,	1),
(2,	'group',	'delete-forum-post',	'forums',	2,	1),
(2,	'group',	'delete-forum-thread',	'forums',	1,	1),
(2,	'group',	'delete-forum-thread',	'forums',	2,	1),
(2,	'group',	'edit-forum-post',	'forums',	1,	1),
(2,	'group',	'edit-forum-post',	'forums',	2,	1),
(2,	'group',	'edit-forum-thread',	'forums',	1,	1),
(2,	'group',	'edit-forum-thread',	'forums',	2,	1),
(2,	'group',	'view-private-forum',	'forums',	1,	1),
(2,	'group',	'view-private-forum',	'forums',	2,	1),
(3,	'group',	'create-pms',	'',	0,	0),
(3,	'group',	'create-public-post',	'',	0,	0),
(3,	'group',	'create-public-thread',	'',	0,	0),
(3,	'group',	'delete-own-pms',	'',	0,	0),
(3,	'group',	'rename-own-thread',	'',	0,	0),
(3,	'group',	'update-own-post',	'',	0,	0),
(3,	'group',	'update-own-profile',	'',	0,	0),
(3,	'group',	'use-post-layout',	'',	0,	0),
(3,	'group',	'view-own-pms',	'',	0,	0),
(4,	'group',	'create-private-forum-post',	'forums',	2,	0),
(4,	'group',	'create-private-forum-thread',	'forums',	2,	0),
(4,	'group',	'delete-forum-post',	'forums',	2,	0),
(4,	'group',	'delete-forum-thread',	'forums',	2,	0),
(4,	'group',	'edit-forum-post',	'forums',	2,	0),
(4,	'group',	'edit-forum-thread',	'forums',	2,	0),
(4,	'group',	'has-displayname',	'',	0,	0),
(4,	'group',	'view-private-forum',	'forums',	2,	0),
(5,	'group',	'ban-users',	'',	0,	0),
(5,	'group',	'create-forum-announcements',	'',	0,	0),
(5,	'group',	'delete-post',	'',	0,	0),
(5,	'group',	'delete-thread',	'',	0,	0),
(5,	'group',	'override-closed',	'',	0,	0),
(5,	'group',	'update-post',	'',	0,	0),
(5,	'group',	'update-thread',	'',	0,	0),
(5,	'group',	'view-post-history',	'',	0,	0),
(6,	'group',	'create-all-private-forum-posts',	'',	0,	0),
(6,	'group',	'create-all-private-forum-threads',	'',	0,	0),
(6,	'group',	'edit-all-group',	'',	0,	0),
(6,	'group',	'edit-attentions-box',	'',	0,	0),
(6,	'group',	'edit-forums',	'',	0,	0),
(6,	'group',	'edit-ip-bans',	'',	0,	0),
(6,	'group',	'edit-permissions',	'',	0,	0),
(6,	'group',	'edit-titles',	'',	0,	0),
(6,	'group',	'edit-users',	'',	0,	0),
(6,	'group',	'has-customusercolor',	'',	0,	0),
(6,	'group',	'manage-board',	'',	0,	0),
(6,	'group',	'override-readonly-forums',	'',	0,	0),
(6,	'group',	'update-profiles',	'',	0,	0),
(6,	'group',	'view-all-private-forums',	'',	0,	0),
(6,	'group',	'view-post-ips',	'',	0,	0),
(7,	'group',	'no-restrictions',	'',	0,	0);

-- 2019-04-11 17:19:02

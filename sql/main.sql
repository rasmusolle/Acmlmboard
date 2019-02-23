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
  `trash` int(1) NOT NULL,
  `readonly` int(1) NOT NULL DEFAULT '0',
  `announce` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `forums` (`id`, `cat`, `ord`, `title`, `descr`, `threads`, `posts`, `lastdate`, `lastuser`, `lastid`, `private`, `trash`, `readonly`, `announce`) VALUES
(1,	1,	1,	'General Forum',	'General topics forum',	0,	0,	0,	0,	0,	0,	1,	0,	0),
(2,	2,	1,	'General Staff Forum',	'Generic Staff Forum					',	0,	0,	0,	0,	0,	1,	1,	0,	0);

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
  `nc0` varchar(6) NOT NULL,
  `nc1` varchar(6) NOT NULL,
  `nc2` varchar(6) NOT NULL,
  `inherit_group_id` int(11) NOT NULL,
  `default` int(2) NOT NULL,
  `banned` int(2) NOT NULL,
  `sortorder` int(11) NOT NULL DEFAULT '0',
  `visible` int(1) NOT NULL DEFAULT '0',
  `primary` int(1) NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

INSERT INTO `group` (`id`, `title`, `nc0`, `nc1`, `nc2`, `inherit_group_id`, `default`, `banned`, `sortorder`, `visible`, `primary`, `description`) VALUES
(1,	'Base User',	'',	'',	'',	0,	0,	0,	100,	0,	0,	''),
(2,	'Normal User',	'97ACEF',	'F185C9',	'7C60B0',	1,	1,	0,	200,	1,	1,	'Normal Registered User'),
(3,	'Moderator',	'AFFABE',	'C762F2',	'47B53C',	10,	0,	0,	600,	1,	1,	''),
(4,	'Administrator',	'FFEA95',	'C53A9E',	'F0C413',	3,	0,	0,	700,	1,	1,	''),
(6,	'Root Administrator',	'EE4444',	'E63282',	'AA3C3C',	0,	-1,	0,	800,	1,	1,	''),
(9,	'Banned',	'888888',	'888888',	'888888',	2,	0,	1,	0,	1,	1,	''),
(10,	'Staff',	'',	'',	'',	2,	0,	0,	300,	0,	0,	''),
(11,	'Disable PM Activity',	'',	'',	'',	0,	0,	0,	1000,	1,	0,	'Disallows all Private Message activity (viewing, creation, deletion)'),
(15,	'Bot',	'',	'',	'',	1,	0,	0,	50,	0,	0,	'');

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
  `emailaddress` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  PRIMARY KEY (`field`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `misc` (`field`, `intval`, `txtval`, `emailaddress`) VALUES
('views',	0,	'',	'0'),
('maxpostsday',	0,	'',	'0'),
('maxpostsdaydate',	0,	'',	'0'),
('maxpostshour',	0,	'',	'0'),
('maxpostshourdate',	0,	'',	'0'),
('maxusers',	0,	'',	'0'),
('maxusersdate',	0,	'',	'0'),
('maxuserstext',	0,	'',	'0'),
('botviews',	0,	'',	'0'),
('attention',	0,	'',	'0');

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
('edit-smilies',	'Edit Smilies',	'',	3,	''),
('edit-titles',	'Edit Titles',	'',	3,	''),
('edit-user-customnickcolor',	'Edit User Custom Nick Color',	'',	3,	'users'),
('edit-user-displayname',	'Edit User Displayname',	'',	3,	'users'),
('edit-user-show-online',	'Edit User Show Online',	'',	3,	''),
('edit-user-title',	'Edit User Title',	'',	3,	'users'),
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
('show-online',	'Show Online',	'',	1,	''),
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
('view-forum-post-history',	'View Forum Post History',	'',	2,	'forums'),
('view-hidden-users',	'View Hidden Users',	'',	3,	''),
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


DROP TABLE IF EXISTS `ranks`;
CREATE TABLE `ranks` (
  `rs` int(10) NOT NULL,
  `p` int(10) NOT NULL DEFAULT '0',
  `str` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `ranks` (`rs`, `p`, `str`) VALUES
(1,	0,	'Non-poster'),
(1,	1,	'Newcomer'),
(1,	20,	'<img src=img/ranks/goomba.gif width=16 height=16><br>Goomba'),
(1,	10,	'<img src=img/ranks/microgoomba.gif width=8 height=9><br>Micro-Goomba'),
(1,	35,	'<img src=img/ranks/redgoomba.gif width=16 height=16><br>Red Goomba'),
(1,	50,	'<img src=img/ranks/redparagoomba.gif width=20 height=24><br>Red Paragoomba'),
(1,	65,	'<img src=img/ranks/paragoomba.gif width=20 height=24><br>Paragoomba'),
(1,	80,	'<img src=img/ranks/shyguy.gif width=16 height=16><br>Shyguy'),
(1,	100,	'<img src=img/ranks/koopa.gif width=16 height=27><br>Koopa'),
(1,	120,	'<img src=img/ranks/redkoopa.gif width=16 height=27><br>Red Koopa'),
(1,	140,	'<img src=img/ranks/paratroopa.gif width=16 height=28><br>Paratroopa'),
(1,	160,	'<img src=img/ranks/redparatroopa.gif width=16 height=28><br>Red Paratroopa'),
(1,	180,	'<img src=img/ranks/cheepcheep.gif width=16 height=16><br>Cheep-cheep'),
(1,	200,	'<img src=img/ranks/redcheepcheep.gif width=16 height=16><br>Red Cheep-cheep'),
(1,	225,	'<img src=img/ranks/ninji.gif width=16 height=16><br>Ninji'),
(1,	250,	'<img src=img/ranks/flurry.gif width=16 height=16><br>Flurry'),
(1,	275,	'<img src=img/ranks/snifit.gif width=16 height=16><br>Snifit'),
(1,	300,	'<img src=img/ranks/porcupo.gif width=16 height=16><br>Porcupo'),
(1,	325,	'<img src=img/ranks/panser.gif width=16 height=16><br>Panser'),
(1,	350,	'<img src=img/ranks/mole.gif width=16 height=16><br>Mole'),
(1,	375,	'<img src=img/ranks/beetle.gif width=16 height=16><br>Buzzy Beetle'),
(1,	400,	'<img src=img/ranks/nipperplant.gif width=16 height=16><br>Nipper Plant'),
(1,	425,	'<img src=img/ranks/bloober.gif width=16 height=16><br>Bloober'),
(1,	450,	'<img src=img/ranks/busterbeetle.gif width=16 height=15><br>Buster Beetle'),
(1,	475,	'<img src=img/ranks/beezo.gif width=16 height=16><br>Beezo'),
(1,	500,	'<img src=img/ranks/bulletbill.gif width=16 height=14><br>Bullet Bill'),
(1,	525,	'<img src=img/ranks/rex.gif width=20 height=32><br>Rex'),
(1,	550,	'<img src=img/ranks/lakitu.gif width=16 height=24><br>Lakitu'),
(1,	575,	'<img src=img/ranks/spiny.gif width=16 height=16><br>Spiny'),
(1,	600,	'<img src=img/ranks/bobomb.gif width=16 height=16><br>Bob-Omb'),
(1,	700,	'<img src=img/ranks/spike.gif width=32 height=32><br>Spike'),
(1,	675,	'<img src=img/ranks/pokey.gif width=18 height=64><br>Pokey'),
(1,	650,	'<img src=img/ranks/cobrat.gif width=16 height=32><br>Cobrat'),
(1,	725,	'<img src=img/ranks/hedgehog.gif width=16 height=24><br>Melon Bug'),
(1,	750,	'<img src=img/ranks/lanternghost.gif width=26 height=19><br>Lantern Ghost'),
(1,	775,	'<img src=img/ranks/fuzzy.gif width=32 height=31><br>Fuzzy'),
(1,	800,	'<img src=img/ranks/bandit.gif width=23 height=28><br>Bandit'),
(1,	830,	'<img src=img/ranks/superkoopa.gif width=23 height=13><br>Super Koopa'),
(1,	860,	'<img src=img/ranks/redsuperkoopa.gif width=23 height=13><br>Red Super Koopa'),
(1,	900,	'<img src=img/ranks/boo.gif width=16 height=16><br>Boo'),
(1,	925,	'<img src=img/ranks/boo2.gif width=16 height=16><br>Boo'),
(1,	950,	'<img src=img/ranks/fuzzball.gif width=16 height=16><br>Fuzz Ball'),
(1,	1000,	'<img src=img/ranks/boomerangbrother.gif width=60 height=40><br>Boomerang Brother'),
(1,	1050,	'<img src=img/ranks/hammerbrother.gif width=60 height=40><br>Hammer Brother'),
(1,	1100,	'<img src=img/ranks/firebrother.gif width=60 height=24><br>Fire Brother'),
(1,	1150,	'<img src=img/ranks/firesnake.gif width=45 height=36><br>Fire Snake'),
(1,	1200,	'<img src=img/ranks/giantgoomba.gif width=24 height=23><br>Giant Goomba'),
(1,	1250,	'<img src=img/ranks/giantkoopa.gif width=24 height=31><br>Giant Koopa'),
(1,	1300,	'<img src=img/ranks/giantredkoopa.gif width=24 height=31><br>Giant Red Koopa'),
(1,	1350,	'<img src=img/ranks/giantparatroopa.gif width=24 height=31><br>Giant Paratroopa'),
(1,	1400,	'<img src=img/ranks/giantredparatroopa.gif width=24 height=31><br>Giant Red Paratroopa'),
(1,	1450,	'<img src=img/ranks/chuck.gif width=28 height=27><br>Chuck'),
(1,	1500,	'<img src=img/ranks/thwomp.gif width=44 height=32><br>Thwomp'),
(1,	1550,	'<img src=img/ranks/bigcheepcheep.gif width=24 height=32><br>Boss Bass'),
(1,	1600,	'<img src=img/ranks/volcanolotus.gif width=32 height=30><br>Volcano Lotus'),
(1,	1650,	'<img src=img/ranks/lavalotus.gif width=24 height=32><br>Lava Lotus'),
(1,	1700,	'<img src=img/ranks/ptooie2.gif width=16 height=43><br>Ptooie'),
(1,	1800,	'<img src=img/ranks/sledgebrother.gif width=60 height=50><br>Sledge Brother'),
(1,	1900,	'<img src=img/ranks/boomboom.gif width=28 height=26><br>Boomboom'),
(1,	2000,	'<img src=img/ranks/birdopink.gif width=60 height=36><br>Birdo'),
(1,	2100,	'<img src=img/ranks/birdored.gif width=60 height=36><br>Red Birdo'),
(1,	2200,	'<img src=img/ranks/birdogreen.gif width=60 height=36><br>Green Birdo'),
(1,	2300,	'<img src=img/ranks/iggy.gif width=28><br>Larry Koopa'),
(1,	2400,	'<img src=img/ranks/morton.gif width=34><br>Morton Koopa'),
(1,	2500,	'<img src=img/ranks/wendy.gif width=28><br>Wendy Koopa'),
(1,	2600,	'<img src=img/ranks/larry.gif width=28><br>Iggy Koopa'),
(1,	2700,	'<img src=img/ranks/roy.gif width=34><br>Roy Koopa'),
(1,	2800,	'<img src=img/ranks/lemmy.gif width=28><br>Lemmy Koopa'),
(1,	2900,	'<img src=img/ranks/ludwig.gif width=33><br>Ludwig Von Koopa'),
(1,	3000,	'<img src=img/ranks/triclyde.gif width=40 height=48><br>Triclyde'),
(1,	3100,	'<img src=img/ranks/kamek.gif width=45 height=34><br>Magikoopa'),
(1,	3200,	'<img src=img/ranks/wart.gif width=40 height=47><br>Wart'),
(1,	3300,	'<img src=img/ranks/babybowser.gif width=36 height=36><br>Baby Bowser'),
(1,	3400,	'<img src=img/ranks/bowser.gif width=52 height=49><br>King Bowser Koopa'),
(1,	3500,	'<img src=img/ranks/yoshi.gif width=31 height=33><br>Yoshi'),
(1,	3600,	'<img src=img/ranks/yoshiyellow.gif width=31 height=32><br>Yellow Yoshi'),
(1,	3700,	'<img src=img/ranks/yoshiblue.gif width=36 height=35><br>Blue Yoshi'),
(1,	3800,	'<img src=img/ranks/yoshired.gif width=33 height=36><br>Red Yoshi'),
(1,	3900,	'<img src=img/ranks/kingyoshi.gif width=24 height=34><br>King Yoshi'),
(1,	4000,	'<img src=img/ranks/babymario.gif width=28 height=24><br>Baby Mario'),
(1,	4100,	'<img src=img/ranks/luigismall.gif width=15 height=22><br>Luigi'),
(1,	4200,	'<img src=img/ranks/mariosmall.gif width=15 height=20><br>Mario'),
(1,	4300,	'<img src=img/ranks/luigibig.gif width=16 height=30><br>Super Luigi'),
(1,	4400,	'<img src=img/ranks/mariobig.gif width=16 height=28><br>Super Mario'),
(1,	4500,	'<img src=img/ranks/luigifire.gif width=16 height=30><br>Fire Luigi'),
(1,	4600,	'<img src=img/ranks/mariofire.gif width=16 height=28><br>Fire Mario'),
(1,	4700,	'<img src=img/ranks/luigicape.gif width=26 height=30><br>Cape Luigi'),
(1,	4800,	'<img src=img/ranks/mariocape.gif width=26 height=28><br>Cape Mario'),
(1,	4900,	'<img src=img/ranks/luigistar.gif width=16 height=30><br>Star Luigi'),
(1,	5000,	'<img src=img/ranks/mariostar.gif width=16 height=28><br>Star Mario'),
(1,	625,	'<img src=img/ranks/drybones.gif><br>Dry Bones'),
(1,	10000,	'Climbing the ranks again!');

DROP TABLE IF EXISTS `ranksets`;
CREATE TABLE `ranksets` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `ranksets` (`id`, `name`) VALUES
(1,	'Mario'),
(0,	'None');

DROP TABLE IF EXISTS `smilies`;
CREATE TABLE `smilies` (
  `text` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `smilies` (`text`, `url`) VALUES
('-_-',	'img/smilies/annoyed.gif'),
('~:o',	'img/smilies/baby.gif'),
('o_O',	'img/smilies/bigeyes.gif'),
(':D',	'img/smilies/biggrin.gif'),
('o_o',	'img/smilies/blank.gif'),
(';_;',	'img/smilies/cry.gif'),
('^^;;;',	'img/smilies/cute2.gif'),
('^_^',	'img/smilies/cute.gif'),
('@_@',	'img/smilies/dizzy.gif'),
('O_O',	'img/smilies/eek.gif'),
('>:]',	'img/smilies/evil.gif'),
(':eyeshift:',	'img/smilies/eyeshift.gif'),
(':(',	'img/smilies/frown.gif'),
('8-)',	'img/smilies/glasses.gif'),
(':LOL:',	'img/smilies/lol.gif'),
('>:[',	'img/smilies/mad.gif'),
('<_<',	'img/smilies/shiftleft.gif'),
('>_>',	'img/smilies/shiftright.gif'),
('x_x',	'img/smilies/sick.gif'),
(':)',	'img/smilies/smile.gif'),
(':P',	'img/smilies/tongue.gif'),
(':B',	'img/smilies/vamp.gif'),
(';)',	'img/smilies/wink.gif'),
(':S',	'img/smilies/wobbly.gif'),
('>_<',	'img/smilies/yuck.gif'),
(':yes:',	'img/smilies/yes.png'),
(':no:',	'img/smilies/no.png'),
(':heart:',	'img/smilies/heart.gif'),
('w00t',	'img/smilies/woot.gif'),
(':x',	'img/smilies/crossmouth.gif'),
(':|',	'img/smilies/slidemouth.gif'),
(':@',	'img/smilies/dropsmile.gif'),
(':-3',	'img/smilies/wobble.gif'),
('X-P',	'img/smilies/xp.gif'),
('X-3',	'img/smilies/x3.gif'),
('X-D',	'img/smilies/xd.gif'),
(':o',	'img/smilies/dramatic.gif');

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
  `tzoff` float NOT NULL DEFAULT '0',
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
  `hidden` int(1) NOT NULL DEFAULT '0',
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
(38,	4,	'group',	'view-hidden-users',	'',	0,	0),
(39,	4,	'group',	'edit-users',	'',	0,	0),
(40,	2,	'group',	'create-pms',	'',	0,	0),
(41,	2,	'group',	'delete-own-pms',	'',	0,	0),
(42,	2,	'group',	'view-own-pms',	'',	0,	0),
(43,	11,	'group',	'create-pms',	'',	0,	1),
(44,	11,	'group',	'delete-own-pms',	'',	0,	1),
(45,	11,	'group',	'view-own-pms',	'',	0,	1),
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
(61,	4,	'group',	'edit-user-show-online',	'',	0,	0),
(62,	2,	'group',	'show-online',	'',	0,	0),
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
(77,	1,	'group',	'view-forum-post-history',	'forums',	2,	1),
(78,	1,	'group',	'view-private-forum',	'forums',	2,	1),
(81,	10,	'group',	'create-private-forum-post',	'forums',	2,	0),
(82,	10,	'group',	'create-private-forum-thread',	'forums',	2,	0),
(83,	10,	'group',	'delete-forum-post',	'forums',	2,	0),
(84,	10,	'group',	'delete-forum-thread',	'forums',	2,	0),
(85,	10,	'group',	'edit-forum-post',	'forums',	2,	0),
(86,	10,	'group',	'edit-forum-thread',	'forums',	2,	0),
(88,	10,	'group',	'view-forum-post-history',	'forums',	2,	0),
(89,	10,	'group',	'view-private-forum',	'forums',	2,	0),
(92,	11,	'group',	'create-private-forum-post',	'forums',	2,	1),
(93,	11,	'group',	'create-private-forum-thread',	'forums',	2,	1),
(94,	11,	'group',	'delete-forum-post',	'forums',	2,	1),
(95,	11,	'group',	'delete-forum-thread',	'forums',	2,	1),
(96,	11,	'group',	'edit-forum-post',	'forums',	2,	1),
(97,	11,	'group',	'edit-forum-thread',	'forums',	2,	1),
(99,	11,	'group',	'view-forum-post-history',	'forums',	2,	1),
(100,	11,	'group',	'view-private-forum',	'forums',	2,	1),
(103,	1,	'group',	'create-private-forum-post',	'forums',	1,	1),
(104,	1,	'group',	'create-private-forum-thread',	'forums',	1,	1),
(105,	1,	'group',	'delete-forum-post',	'forums',	1,	1),
(106,	1,	'group',	'delete-forum-thread',	'forums',	1,	1),
(107,	1,	'group',	'edit-forum-post',	'forums',	1,	1),
(108,	1,	'group',	'edit-forum-thread',	'forums',	1,	1),
(110,	1,	'group',	'view-forum-post-history',	'forums',	1,	1),
(111,	1,	'group',	'view-private-forum',	'forums',	1,	1),
(114,	11,	'group',	'create-private-forum-post',	'forums',	1,	1),
(115,	11,	'group',	'create-private-forum-thread',	'forums',	1,	1),
(116,	11,	'group',	'delete-forum-post',	'forums',	1,	1),
(117,	11,	'group',	'delete-forum-thread',	'forums',	1,	1),
(118,	11,	'group',	'edit-forum-post',	'forums',	1,	1),
(119,	11,	'group',	'edit-forum-thread',	'forums',	1,	1),
(121,	11,	'group',	'view-forum-post-history',	'forums',	1,	1),
(122,	11,	'group',	'view-private-forum',	'forums',	1,	1),
(123,	1,	'group',	'view-private-category',	'categories',	1,	1),
(124,	11,	'group',	'view-private-category',	'categories',	1,	1),
(125,	4,	'group',	'edit-titles',	'',	0,	0),
(126,	6,	'group',	'show-as-staff',	'',	0,	0);

-- 2019-02-22 16:11:07

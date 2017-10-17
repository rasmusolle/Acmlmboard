-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Värd: 127.0.0.1
-- Tid vid skapande: 14 okt 2017 kl 16:24
-- Serverversion: 5.6.17
-- PHP-version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `acmlmboard`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `annoucenickprefix`
--

CREATE TABLE `annoucenickprefix` (
  `group_id` int(16) NOT NULL,
  `char` varchar(1) NOT NULL,
  `color` varchar(16) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `annoucenickprefix`
--

INSERT INTO `annoucenickprefix` (`group_id`, `char`, `color`) VALUES
(6, '~', 'red'),
(8, '+', 'lt_blue'),
(3, '%', 'lt_green'),
(4, '@', 'orange');

-- --------------------------------------------------------

--
-- Tabellstruktur `announcechans`
--

CREATE TABLE `announcechans` (
  `id` int(11) UNSIGNED NOT NULL,
  `chan` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellstruktur `badgecateg`
--

CREATE TABLE `badgecateg` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `order` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `badgecateg`
--

INSERT INTO `badgecateg` (`id`, `order`, `name`, `description`) VALUES
(1, 1, 'Basic Badge', 'This is a decorative badge assignable only by staff.'),
(2, 2, 'Shop Badge', 'This badge can be purchased in the Badge Shop'),
(3, 3, 'Achievement Badge', 'This badge can only be earned. This badge is automatically assigned by the board.');

-- --------------------------------------------------------

--
-- Tabellstruktur `badges`
--

CREATE TABLE `badges` (
  `id` int(11) UNSIGNED NOT NULL,
  `image` varchar(48) NOT NULL,
  `priority` mediumint(4) UNSIGNED NOT NULL DEFAULT '1',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `name` varchar(48) NOT NULL DEFAULT '',
  `description` varchar(100) NOT NULL DEFAULT '',
  `inherit` int(11) DEFAULT NULL,
  `posttext` varchar(10) DEFAULT NULL,
  `effect` varchar(64) DEFAULT NULL,
  `effect_variable` varchar(128) DEFAULT NULL,
  `coins` mediumint(8) DEFAULT NULL,
  `coins2` mediumint(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumpning av Data i tabell `badges`
--

INSERT INTO `badges` (`id`, `image`, `priority`, `type`, `name`, `description`, `inherit`, `posttext`, `effect`, `effect_variable`, `coins`, `coins2`) VALUES
(1, 'img/badges/pmbadge.png', 100, 1, 'P! Badge', 'P! Power badge. This is given by Emuz to show thanks.', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'img/badges/glasses.png', 50, 1, 'X-Ray Resistance Glasses', 'Ahh hardened for X-Rays? I bet it\'s to see those HTML comments..', NULL, NULL, 'show-html-comments', NULL, NULL, NULL),
(3, 'img/badges/quatloo.png', 15, 1, 'Quatloo Challenge Winner!', 'Given upon completion of some silly challenge of Emuz\'s', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'img/badges/1milthview.png', 15, 1, 'Got X,000,000th view', 'Got X,000,000th board view', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellstruktur `blockedlayouts`
--

CREATE TABLE `blockedlayouts` (
  `user` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `blockee` mediumint(8) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellstruktur `categories`
--

CREATE TABLE `categories` (
  `id` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `ord` tinyint(4) NOT NULL,
  `minpower` tinyint(4) NOT NULL,
  `private` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `categories`
--

INSERT INTO `categories` (`id`, `title`, `ord`, `minpower`, `private`) VALUES
(1, 'General Forums', 2, 0, 0),
(2, 'Staff Forums', 0, 1, 1);

-- --------------------------------------------------------

--
-- Tabellstruktur `dailystats`
--

CREATE TABLE `dailystats` (
  `date` char(8) NOT NULL,
  `users` int(11) DEFAULT '0',
  `threads` int(11) DEFAULT '0',
  `posts` int(11) DEFAULT '0',
  `views` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `dailystats`
--

INSERT INTO `dailystats` (`date`, `users`, `threads`, `posts`, `views`) VALUES
('10-14-17', 1, 0, 0, 35);

-- --------------------------------------------------------

--
-- Tabellstruktur `deletedgroups`
--

CREATE TABLE `deletedgroups` (
  `id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `char` varchar(1) NOT NULL,
  `color` varchar(16) DEFAULT NULL,
  `nc0` varchar(6) CHARACTER SET utf8 NOT NULL,
  `nc1` varchar(6) CHARACTER SET utf8 NOT NULL,
  `nc2` varchar(6) CHARACTER SET utf8 NOT NULL,
  `inherit_group_id` int(11) NOT NULL,
  `default` int(2) NOT NULL,
  `banned` int(2) NOT NULL,
  `sortorder` int(11) NOT NULL DEFAULT '0',
  `visible` int(1) NOT NULL DEFAULT '0',
  `primary` int(1) NOT NULL DEFAULT '0',
  `description` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `month` tinyint(4) NOT NULL,
  `day` tinyint(4) NOT NULL,
  `year` smallint(6) NOT NULL,
  `user` mediumint(9) NOT NULL,
  `private` tinyint(4) NOT NULL,
  `event_title` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `forummods`
--

CREATE TABLE `forummods` (
  `uid` int(12) NOT NULL,
  `fid` int(12) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `forums`
--

CREATE TABLE `forums` (
  `id` int(5) NOT NULL DEFAULT '0',
  `cat` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `ord` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `descr` varchar(255) NOT NULL,
  `threads` mediumint(8) NOT NULL DEFAULT '0',
  `posts` mediumint(8) NOT NULL DEFAULT '0',
  `lastdate` int(11) NOT NULL DEFAULT '0',
  `lastuser` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `lastid` int(11) NOT NULL,
  `minpower` tinyint(4) NOT NULL DEFAULT '-1',
  `minpowerthread` tinyint(4) NOT NULL DEFAULT '0',
  `minpowerreply` tinyint(4) NOT NULL DEFAULT '0',
  `private` int(1) NOT NULL,
  `trash` int(1) NOT NULL,
  `announcechan_id` int(11) NOT NULL DEFAULT '0',
  `readonly` int(1) NOT NULL DEFAULT '0',
  `announce` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `forums`
--

INSERT INTO `forums` (`id`, `cat`, `ord`, `title`, `descr`, `threads`, `posts`, `lastdate`, `lastuser`, `lastid`, `minpower`, `minpowerthread`, `minpowerreply`, `private`, `trash`, `announcechan_id`, `readonly`, `announce`) VALUES
(1, 1, 1, 'General Forum', 'General topics forum', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 0, 0),
(2, 2, 1, 'General Staff Forum', 'Generic Staff Forum					', 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 1, 0, 0);

-- --------------------------------------------------------

--
-- Tabellstruktur `forumsread`
--

CREATE TABLE `forumsread` (
  `uid` mediumint(9) NOT NULL,
  `fid` int(5) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `forumsread`
--

INSERT INTO `forumsread` (`uid`, `fid`, `time`) VALUES
(1, 1, 1507994275),
(1, 2, 1507994275);

-- --------------------------------------------------------

--
-- Tabellstruktur `group`
--

CREATE TABLE `group` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `char` varchar(1) CHARACTER SET latin1 NOT NULL,
  `irc_color` varchar(16) CHARACTER SET latin1 DEFAULT NULL,
  `nc0` varchar(6) NOT NULL,
  `nc1` varchar(6) NOT NULL,
  `nc2` varchar(6) NOT NULL,
  `inherit_group_id` int(11) NOT NULL,
  `default` int(2) NOT NULL,
  `banned` int(2) NOT NULL,
  `sortorder` int(11) NOT NULL DEFAULT '0',
  `visible` int(1) NOT NULL DEFAULT '0',
  `primary` int(1) NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumpning av Data i tabell `group`
--

INSERT INTO `group` (`id`, `title`, `char`, `irc_color`, `nc0`, `nc1`, `nc2`, `inherit_group_id`, `default`, `banned`, `sortorder`, `visible`, `primary`, `description`) VALUES
(1, 'Base User', '', NULL, '', '', '', 0, 0, 0, 100, 0, 0, ''),
(2, 'Normal User', '', NULL, '97ACEF', 'F185C9', '7C60B0', 1, 1, 0, 200, 1, 1, 'Normal Registered User'),
(3, 'Global Moderator', '%', 'lt_green', 'AFFABE', 'C762F2', '47B53C', 8, 0, 0, 600, 1, 1, ''),
(4, 'Administrator', '@', 'orange', 'FFEA95', 'C53A9E', 'F0C413', 3, 0, 0, 700, 1, 1, ''),
(6, 'Root Administrator', '~', 'red', 'EE4444', 'E63282', 'AA3C3C', 0, -1, 0, 800, 1, 1, ''),
(8, 'Local Moderator', '+', 'lt_blue', 'D8E8FE', 'FFB3F3', 'EEB9BA', 10, 0, 0, 400, 1, 1, ''),
(9, 'Banned', '', NULL, '888888', '888888', '888888', 2, 0, 1, 0, 1, 1, ''),
(10, 'Staff', '', NULL, '', '', '', 2, 0, 0, 300, 0, 0, ''),
(11, 'Disable PM Activity', '', NULL, '', '', '', 0, 0, 0, 1000, 1, 0, 'Disallows all Private Message activity (viewing, creation, deletion)'),
(13, 'General Forum Moderation', '', NULL, '', '', '', 0, 0, 0, 450, 1, 0, 'Allows moderation of the General Forum'),
(15, 'Bot', '', NULL, '', '', '', 1, 0, 0, 50, 0, 0, '');

-- --------------------------------------------------------

--
-- Tabellstruktur `guests`
--

CREATE TABLE `guests` (
  `date` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ipbanned` tinyint(4) NOT NULL DEFAULT '0',
  `useragent` varchar(255) NOT NULL,
  `bot` int(11) NOT NULL,
  `lastforum` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `guests`
--

INSERT INTO `guests` (`date`, `ip`, `url`, `ipbanned`, `useragent`, `bot`, `lastforum`) VALUES
(1507998199, '127.0.0.1', '/Acmlmboard/index.php', 0, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36', 0, 0);

-- --------------------------------------------------------

--
-- Tabellstruktur `hourlyviews`
--

CREATE TABLE `hourlyviews` (
  `hour` mediumint(9) NOT NULL,
  `views` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `hourlyviews`
--

INSERT INTO `hourlyviews` (`hour`, `views`) VALUES
(418886, 3),
(418887, 23),
(418888, 9);

-- --------------------------------------------------------

--
-- Tabellstruktur `ignoredforums`
--

CREATE TABLE `ignoredforums` (
  `uid` int(11) NOT NULL,
  `fid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellstruktur `ip2c`
--

CREATE TABLE `ip2c` (
  `ip_from` bigint(12) NOT NULL,
  `ip_to` bigint(12) NOT NULL,
  `registrar` varchar(50) NOT NULL,
  `assigned` int(12) NOT NULL,
  `cc2` varchar(2) NOT NULL,
  `cc3` varchar(3) NOT NULL,
  `cname` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `ipbans`
--

CREATE TABLE `ipbans` (
  `ipmask` varchar(15) NOT NULL,
  `hard` tinyint(1) NOT NULL,
  `expires` int(12) NOT NULL,
  `banner` varchar(25) NOT NULL,
  `reason` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `itemcateg`
--

CREATE TABLE `itemcateg` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `corder` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `itemcateg`
--

INSERT INTO `itemcateg` (`id`, `corder`, `name`, `description`) VALUES
(1, 1, 'Weapons', 'boom boom boom'),
(2, 2, 'Armor', 'Bling! Well, until I think of a better description to put here, or something ...'),
(3, 3, 'Shields', 'More bling, or something'),
(4, 4, 'Helms', 'Bling again, but on the head this time'),
(5, 5, 'Boots', 'Vroom! But without a motor'),
(6, 6, 'Accessories', 'Notepad, Paint, Calculator, DOS prompt, Wordpad');

-- --------------------------------------------------------

--
-- Tabellstruktur `items`
--

CREATE TABLE `items` (
  `id` int(8) NOT NULL,
  `cat` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `type` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL,
  `desc` varchar(255) NOT NULL DEFAULT 'No description.',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `stype` varchar(9) NOT NULL DEFAULT 'mmaaaaaaa',
  `sHP` smallint(5) NOT NULL DEFAULT '100',
  `sMP` smallint(5) NOT NULL DEFAULT '100',
  `sAtk` smallint(5) NOT NULL DEFAULT '0',
  `sDef` smallint(5) NOT NULL DEFAULT '0',
  `sInt` smallint(5) NOT NULL DEFAULT '0',
  `sMDf` smallint(5) NOT NULL DEFAULT '0',
  `sDex` smallint(5) NOT NULL DEFAULT '0',
  `sLck` smallint(5) NOT NULL DEFAULT '0',
  `sSpd` smallint(5) NOT NULL DEFAULT '0',
  `coins` mediumint(8) NOT NULL DEFAULT '0',
  `coins2` mediumint(9) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `items`
--

INSERT INTO `items` (`id`, `cat`, `type`, `name`, `desc`, `hidden`, `stype`, `sHP`, `sMP`, `sAtk`, `sDef`, `sInt`, `sMDf`, `sDex`, `sLck`, `sSpd`, `coins`, `coins2`) VALUES
(0, 0, 0, 'Nothing', 'Nothing.  At All.', 0, 'aaaaaaaaa', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellstruktur `log`
--

CREATE TABLE `log` (
  `t` int(12) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `uid` int(11) NOT NULL,
  `request` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `mcache`
--

CREATE TABLE `mcache` (
  `hash` varchar(32) NOT NULL,
  `file` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `misc`
--

CREATE TABLE `misc` (
  `field` varchar(255) NOT NULL,
  `intval` int(11) NOT NULL DEFAULT '0',
  `txtval` text NOT NULL,
  `emailaddress` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `misc`
--

INSERT INTO `misc` (`field`, `intval`, `txtval`, `emailaddress`) VALUES
('views', 35, '', '0'),
('maxpostsday', 0, '', '0'),
('maxpostsdaydate', 0, '', '0'),
('maxpostshour', 0, '', '0'),
('maxpostshourdate', 0, '', '0'),
('maxusers', 1, '', '0'),
('maxusersdate', 1507998183, '', '0'),
('maxuserstext', 0, '<a href="profile.php?id=1"><span  style=\'color:#AA3C3C;\'>admin</span></a>', '0'),
('botviews', 0, '', '0'),
('lockdown', 0, '', '0'),
('attention', 0, '', '0'),
('regdisable', 0, '', '0'),
('hacksnews', 0, '', '0'),
('boardemail', 0, '', '0');

-- --------------------------------------------------------

--
-- Tabellstruktur `mood`
--

CREATE TABLE `mood` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `label` varchar(127) NOT NULL DEFAULT '',
  `local` tinyint(1) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `perm`
--

CREATE TABLE `perm` (
  `id` varchar(64) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `permcat_id` int(11) NOT NULL,
  `permbind_id` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumpning av Data i tabell `perm`
--

INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES
('admin-tools-access', 'Access to Admin Tools', '', 3, ''),
('assign-secondary-groups', 'Assign Secondary Groups', '', 3, ''),
('ban-users', 'Ban Users', '', 3, ''),
('banned', 'Is Banned', '', 2, ''),
('block-layout', 'Enable Layout Blocking', 'Enables per-user layout blocking', 3, ''),
('bypass-lockdown', 'View Board Under Lockdown', '', 3, ''),
('bypass-logo-override', 'Bypass Logo Overrides', 'Bypasses any board-wide logo locks.', 3, ''),
('bypass-theme-override', 'Bypass Theme Overrides', 'Bypasses any board-wide theme locks.', 3, ''),
('can-edit-group', 'Edit Group Assets', '', 3, 'group'),
('can-edit-group-member', 'Edit User Assets', '', 3, 'group'),
('capture-sprites', 'Capture Sprites', '', 1, ''),
('consecutive-posts', 'Consecutive Posts', '', 2, ''),
('create-all-forums-announcement', 'Create All Forums Announcement', '', 4, ''),
('create-all-private-forum-posts', 'Create All Private Forum Posts', '', 3, ''),
('create-all-private-forum-threads', 'Create All Private Forum Threads', '', 3, ''),
('create-consecutive-forum-post', 'Create Consecutive Forum Post', '', 2, 'forums'),
('create-forum-announcement', 'Create Forum Announcement', '', 3, 'forums'),
('create-pms', 'Create PMs', '', 1, ''),
('create-private-forum-post', 'Create Private Forum Post', '', 2, 'forums'),
('create-private-forum-thread', 'Create Private Forum Thread', '', 2, 'forums'),
('create-public-post', 'Create Public Post', '', 4, ''),
('create-public-thread', 'Create Public Thread', '', 4, ''),
('delete-forum-post', 'Delete Forum Post', '', 2, 'forums'),
('delete-forum-thread', 'Delete Forum Thread', '', 2, 'forums'),
('delete-own-pms', 'Delete Own PMs', '', 1, ''),
('delete-post', 'Delete Post', '', 2, ''),
('delete-thread', 'Delete Thread', '', 2, ''),
('delete-user-pms', 'Delete User PMs', '', 3, ''),
('deleted-posts-tracker', 'Can Use Deleted Posts Tracker', '', 2, ''),
('edit-all-group', 'Edit All Group Assets', '', 3, ''),
('edit-all-group-member', 'Edit All User Assets', '', 3, ''),
('edit-attentions-box', 'Edit Attentions Box', '', 3, ''),
('edit-badges', 'Edit Badges', '', 3, ''),
('edit-calendar-events', 'Edit Calendar Events', '', 3, ''),
('edit-categories', 'Edit Categories', '', 3, ''),
('edit-customusercolors', 'Edit Custom Username Colors', '', 3, ''),
('edit-displaynames', 'Edit Displaynames', '', 3, ''),
('edit-forum-post', 'Edit Forum Post', '', 2, 'forums'),
('edit-forum-thread', 'Edit Forum Thread', '', 2, 'forums'),
('edit-forums', 'Edit Forums', '', 3, ''),
('edit-groups', 'Edit Groups', '', 3, ''),
('edit-ip-bans', 'Edit IP Bans', '', 0, ''),
('edit-moods', 'Edit Moods', '', 3, ''),
('edit-own-permissions', 'Edit Own Permissions', '', 3, ''),
('edit-own-title', 'Edit Own Title', '', 3, ''),
('edit-permissions', 'Edit Permissions', '', 3, ''),
('edit-post-icons', 'Edit Post Icons', '', 3, ''),
('edit-profileext', 'Edit Extended Profile Fields', '', 3, ''),
('edit-ranks', 'Edit Ranks', '', 3, ''),
('edit-smilies', 'Edit Smilies', '', 3, ''),
('edit-spiders', 'Edit Spiders', '', 3, ''),
('edit-sprites', 'Edit Sprites', '', 3, ''),
('edit-titles', 'Edit Titles', '', 3, ''),
('edit-user-badges', 'Assign User Badges', '', 3, ''),
('edit-user-customnickcolor', 'Edit User Custom Nick Color', '', 3, 'users'),
('edit-user-displayname', 'Edit User Displayname', '', 3, 'users'),
('edit-user-show-online', 'Edit User Show Online', '', 3, ''),
('edit-user-title', 'Edit User Title', '', 3, 'users'),
('edit-users', 'Edit Users', '', 3, ''),
('has-customusercolor', 'Can Edit Custom Username Color', '', 3, ''),
('has-displayname', 'Can Use Displayname', '', 3, ''),
('ignore-thread-time-limit', 'Ignore Thread Time Limit', '', 0, ''),
('login', 'Login', '', 1, ''),
('manage-board', 'Administration Management Panel', '', 3, ''),
('manage-shop-items', 'Manage Shop Items', '', 3, ''),
('mark-read', 'Mark Read', '', 1, ''),
('no-restrictions', 'No Restrictions', '', 3, ''),
('override-closed-all', 'Post in All Closed Threads', '', 2, ''),
('override-closed-forum', 'Post in Closed Threads in Forum', '', 2, 'forums'),
('override-closed-thread', 'Post in Closed Thread', '', 2, 'threads'),
('override-readonly-forums', 'Override Read Only Forums', '', 3, ''),
('post-offline', 'Post Offline', '', 4, ''),
('post-radar', 'Post Radar', 'Can use Post Radar', 2, ''),
('rate-thread', 'Rate Thread', '', 1, ''),
('register', 'Register', '', 1, ''),
('rename-own-thread', 'Rename Own Thread', '', 1, ''),
('show-as-staff', 'Listed Publicly as Staff', '', 3, 'users'),
('show-online', 'Show Online', '', 1, ''),
('staff', 'Is Staff', '', 2, ''),
('track-deleted-posts', 'Can Track All Deleted Posts', '', 2, ''),
('track-ip-change', 'Track IP Changes in IRC', 'Add this to a group or user to have their IP change reported to the staff channel.', 3, ''),
('update-extended-profiles', 'Update Extended Profiles', '', 3, ''),
('update-own-extended-profile', 'Update Own Extended Profile', '', 1, ''),
('update-own-moods', 'Update Own Moods', '', 1, ''),
('update-own-post', 'Update Own Post', '', 4, ''),
('update-own-profile', 'Update Own Profile', '', 1, ''),
('update-post', 'Update Post', '', 2, ''),
('update-profiles', 'Update Profiles', '', 3, ''),
('update-thread', 'Update Thread', '', 2, ''),
('update-user-extended-profile', 'Update User Extended Profile', '', 3, 'users'),
('update-user-moods', 'Update User Moods', '', 3, 'users'),
('update-user-profile', 'Update User Profile', '', 3, 'users'),
('use-item-shop', 'Use Item Shop', '', 1, ''),
('use-post-layout', 'Use Post Layout', '', 4, ''),
('use-test-bed', 'Use Test Bed', '', 3, ''),
('use-uploader', 'Use Uploader', '', 1, ''),
('view-acs-calendar', 'View ACS Rankings Calendar', '', 2, ''),
('view-all-private-categories', 'View All Private Categories', '', 3, ''),
('view-all-private-forums', 'View All Private Forums', '', 3, ''),
('view-all-private-posts', 'View All Private Posts', '', 3, ''),
('view-all-private-threads', 'View All Private Threads', '', 3, ''),
('view-all-sprites', 'View All Sprites', '', 3, ''),
('view-allranks', 'Show Hidden Ranks', '', 2, ''),
('view-errors', 'View PHP Errors', '', 0, ''),
('view-favorites', 'View Favorite Threads', '', 1, ''),
('view-forum-post-history', 'View Forum Post History', '', 2, 'forums'),
('view-hidden-users', 'View Hidden Users', '', 3, ''),
('view-own-pms', 'View Own PMs', '', 1, ''),
('view-own-sprites', 'View Own Sprites', '', 1, ''),
('view-permissions', 'View Permissions', '', 3, ''),
('view-post-history', 'View Post History', '', 2, ''),
('view-post-ips', 'View Post IP Addresses', '', 3, ''),
('view-private-category', 'View Private Category', '', 2, 'categories'),
('view-private-forum', 'View Private Forum', '', 2, 'forums'),
('view-private-post', 'View Private Post', '', 2, 'posts'),
('view-private-thread', 'View Private Thread', '', 2, 'threads'),
('view-profile-page', 'View Profile Page', '', 1, ''),
('view-public-categories', 'View Public Categories', '', 1, ''),
('view-public-forums', 'View Public Forums', '', 1, ''),
('view-public-posts', 'View Public Posts', '', 1, ''),
('view-public-threads', 'View Public Threads', '', 1, ''),
('view-user-pms', 'View User PMs', '', 3, ''),
('view-user-urls', 'View User URLs', '', 3, '');

-- --------------------------------------------------------

--
-- Tabellstruktur `permbind`
--

CREATE TABLE `permbind` (
  `id` varchar(64) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumpning av Data i tabell `permbind`
--

INSERT INTO `permbind` (`id`, `title`) VALUES
('categories', 'Category'),
('forums', 'Forum'),
('group', 'Group'),
('posts', 'Post'),
('threads', 'Thread'),
('users', 'User');

-- --------------------------------------------------------

--
-- Tabellstruktur `permcat`
--

CREATE TABLE `permcat` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sortorder` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumpning av Data i tabell `permcat`
--

INSERT INTO `permcat` (`id`, `title`, `sortorder`) VALUES
(1, 'Basic', 100),
(2, 'Moderator', 200),
(3, 'Administrative', 300),
(4, 'Posting', 150);

-- --------------------------------------------------------

--
-- Tabellstruktur `perm_permbind`
--

CREATE TABLE `perm_permbind` (
  `perm_id` varchar(64) NOT NULL,
  `permbind_id` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumpning av Data i tabell `perm_permbind`
--

INSERT INTO `perm_permbind` (`perm_id`, `permbind_id`) VALUES
('view-private-category', 'categories'),
('view-private-forum', 'forums'),
('view-private-thread', 'threads'),
('view-private-post', 'posts');

-- --------------------------------------------------------

--
-- Tabellstruktur `pmsgs`
--

CREATE TABLE `pmsgs` (
  `id` int(11) UNSIGNED NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  `mood` int(11) NOT NULL DEFAULT '-1',
  `nolayout` int(1) NOT NULL,
  `ip` char(15) NOT NULL,
  `userto` mediumint(9) UNSIGNED NOT NULL,
  `userfrom` mediumint(9) UNSIGNED NOT NULL,
  `unread` tinyint(4) NOT NULL,
  `del_from` tinyint(1) NOT NULL DEFAULT '0',
  `del_to` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `pmsgstext`
--

CREATE TABLE `pmsgstext` (
  `id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `polloptions`
--

CREATE TABLE `polloptions` (
  `id` int(11) NOT NULL,
  `poll` int(11) NOT NULL,
  `option` varchar(255) NOT NULL,
  `r` smallint(3) NOT NULL,
  `g` smallint(3) NOT NULL,
  `b` smallint(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `polls`
--

CREATE TABLE `polls` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `multivote` int(1) NOT NULL DEFAULT '0',
  `changeable` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `pollvotes`
--

CREATE TABLE `pollvotes` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `posticons`
--

CREATE TABLE `posticons` (
  `id` tinyint(4) UNSIGNED NOT NULL,
  `url` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `posticons`
--

INSERT INTO `posticons` (`id`, `url`) VALUES
(1, 'img/icons/icon1.gif'),
(2, 'img/icons/icon2.gif'),
(3, 'img/icons/icon3.gif'),
(4, 'img/icons/icon4.gif'),
(5, 'img/icons/icon5.gif'),
(6, 'img/icons/icon6.gif'),
(7, 'img/icons/icon7.gif'),
(8, 'img/coin.gif'),
(9, 'img/coin2.gif'),
(10, 'img/smilies/baby.gif'),
(11, 'img/smilies/smile.gif'),
(12, 'img/smilies/wink.gif'),
(13, 'img/smilies/biggrin.gif'),
(14, 'img/smilies/cute.gif'),
(15, 'img/smilies/glasses.gif'),
(16, 'img/smilies/mad.gif'),
(17, 'img/smilies/frown.gif'),
(18, 'img/smilies/yuck.gif'),
(19, 'img/smilies/sick.gif'),
(20, 'img/smilies/wobbly.gif'),
(21, 'img/smilies/eek.gif'),
(22, 'img/smilies/blank.gif'),
(23, 'img/smilies/jawdrop.gif'),
(24, 'img/smilies/bigeyes.gif'),
(25, 'img/smilies/tongue.gif'),
(26, 'img/smilies/vamp.gif'),
(27, 'img/smilies/dizzy.gif'),
(28, 'img/smilies/eyeshift.gif'),
(29, 'img/smilies/shiftleft.gif'),
(30, 'img/smilies/shiftright.gif');

-- --------------------------------------------------------

--
-- Tabellstruktur `posts`
--

CREATE TABLE `posts` (
  `id` int(11) UNSIGNED NOT NULL,
  `user` mediumint(9) UNSIGNED NOT NULL DEFAULT '0',
  `thread` mediumint(9) UNSIGNED NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL DEFAULT '0',
  `mood` int(11) NOT NULL DEFAULT '-1',
  `nolayout` int(1) NOT NULL,
  `nosmilies` int(1) NOT NULL,
  `ip` char(15) NOT NULL,
  `num` mediumint(9) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `announce` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `poststext`
--

CREATE TABLE `poststext` (
  `id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `revision` int(5) NOT NULL DEFAULT '1',
  `date` int(11) NOT NULL,
  `user` mediumint(9) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `post_radar`
--

CREATE TABLE `post_radar` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` mediumint(8) UNSIGNED NOT NULL,
  `user2_id` mediumint(8) UNSIGNED NOT NULL,
  `ctime` bigint(20) UNSIGNED NOT NULL,
  `dtime` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellstruktur `profileext`
--

CREATE TABLE `profileext` (
  `id` varchar(64) NOT NULL DEFAULT '',
  `title` varchar(256) NOT NULL DEFAULT '',
  `sortorder` int(11) NOT NULL DEFAULT '0',
  `fmt` varchar(256) NOT NULL DEFAULT '%s',
  `description` varchar(256) NOT NULL DEFAULT '',
  `icon` varchar(256) NOT NULL DEFAULT '',
  `validation` varchar(256) NOT NULL DEFAULT '',
  `example` varchar(256) NOT NULL DEFAULT '',
  `extrafield` int(1) NOT NULL DEFAULT '0',
  `parser` varchar(256) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumpning av Data i tabell `profileext`
--

INSERT INTO `profileext` (`id`, `title`, `sortorder`, `fmt`, `description`, `icon`, `validation`, `example`, `extrafield`, `parser`) VALUES
('3ds', '3DS Friend Code', 0, '$1-$2-$3', 'Your 3DS Friend Code (hyphens are optional)', '', '([0-9]{4})-?([0-9]{4})-?([0-9]{4})', '1234-5678-9012', 0, ''),
('aim', 'AIM Screen Name', 0, '$0', 'Your AIM Screen Name (or email)', '', '[A-Za-z.%+-_@]+', 'SmarterChild', 0, 'email'),
('ds', 'DS Game Friend Code', 0, '$1-$2-$3', 'Your DS Game Friend Code (hyphens are optional)', '', '([0-9]{4})-?([0-9]{4})-?([0-9]{4})', '1234-5678-9012', 1, ''),
('facebook', 'Facebook', 0, '<a href=http://www.facebook.com/$0>$0</a>', 'Your Facebook ID number or username', '', '[\\.0-9a-zA-Z]+', 'john.smith', 0, ''),
('gplus', 'Google+', 0, '<a href=http://plus.google.com/$0>$0</a>', 'Your Google+ ID (the long ass number)', '', '[0-9]+', '110393731121066107376', 0, ''),
('gtalk', 'Google Talk', 0, '$0', 'Your Google Talk email address', '', '[A-Z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}', 'eric.schmidt@gmail.com', 0, 'email'),
('icq', 'ICQ number', 0, '<a href="http://wwp.icq.com/$0#pager">$0 <img src="http://wwp.icq.com/scripts/online.dll?icq=$0&amp;img=5" border=0></a>', 'Your ICQ Number', '', '[0-9]+', '91235781', 0, ''),
('instagram', 'Instagram', 0, '<a href=http://instagram.com/$0/>$0</a>', 'Your Instagram username (as it appears on a URL)', '', '[_\\.-0-9a-zA-Z]+', 'soviet.russia', 0, ''),
('jabber', 'Jabber', 0, '$0', 'Your Jabber email address', '', '[A-Z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}', 'linus.torvalds@linux.org', 0, 'email'),
('msn', 'Windows Live! ID', 0, '$0', 'Your Windows Live! ID', '', '[A-Z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}', 'bill.gates@hotmail.com', 0, 'email'),
('nintendoid', 'Nintendo ID', 0, '<a href="https://miiverse.nintendo.net/users/$0">$0</a>', 'Your Nintendo ID', '', '[_\\-0-9a-zA-Z]+', 'mariobros.', 0, ''),
('psn', 'PSN', 0, '$0', 'Your PlayStation Network username', '', '[0-9a-zA-Z]+', 'sonyrul3s', 0, ''),
('soundcloud', 'Soundcloud', 0, '<a href=http://soundcloud.com/$0>$0</a>', 'Your Soundcloud username (as it appears on a URL)', '', '[_\\-0-9a-zA-Z]+', 'britney-spears', 0, ''),
('tumblr', 'Tumblr', 0, '<a href=http://$0.tumblr.com/>$0</a>', 'Your Tumblr username (as it appears on a URL)', '', '[_\\-0-9a-zA-Z]+', 'supermariosunshinebeta', 0, ''),
('twitter', 'Twitter', 0, '<a href=http://twitter.com/$0>@$0</a>', 'Your Twitter username (without the leading @)', '', '[_0-9a-zA-Z]+', 'jack', 0, ''),
('wii', 'Wii Game Friend Code', 0, '$1-$2-$3', 'Your Wii Game Friend Code (hyphens are optional)', '', '([0-9]{4})-?([0-9]{4})-?([0-9]{4})', '1234-5678-9012', 1, ''),
('wii-system', 'Wii Friend Code', 0, '$1-$2-$3', 'Your Wii Friend Code (hyphens are optional)', '', '([0-9]{4})-?([0-9]{4})-?([0-9]{4})', '1234-5678-9012', 0, ''),
('xbl', 'XBOX Live', 0, '$0', 'Your XBOX Live username', '', '[0-9a-zA-Z]+', 'n00bpwner', 0, ''),
('yahoo', 'Yahoo! ID', 0, '$email', 'Your Yahoo! ID', '', '[A-Z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}', 'carol.bartz@rocketmail.com', 0, 'email'),
('youtube', 'YouTube', 0, '<a href=http://www.youtube.com/$0>$0</a>', 'Your YouTube username', '', '[0-9a-zA-Z]+', 'spudd', 0, '');

-- --------------------------------------------------------

--
-- Tabellstruktur `ranks`
--

CREATE TABLE `ranks` (
  `rs` int(10) NOT NULL,
  `p` int(10) NOT NULL DEFAULT '0',
  `str` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `ranks`
--

INSERT INTO `ranks` (`rs`, `p`, `str`) VALUES
(1, 0, 'Non-poster'),
(1, 1, 'Newcomer'),
(1, 20, '<img src=img/ranks/goomba.gif width=16 height=16><br>Goomba'),
(1, 10, '<img src=img/ranks/microgoomba.gif width=8 height=9><br>Micro-Goomba'),
(1, 35, '<img src=img/ranks/redgoomba.gif width=16 height=16><br>Red Goomba'),
(1, 50, '<img src=img/ranks/redparagoomba.gif width=20 height=24><br>Red Paragoomba'),
(1, 65, '<img src=img/ranks/paragoomba.gif width=20 height=24><br>Paragoomba'),
(1, 80, '<img src=img/ranks/shyguy.gif width=16 height=16><br>Shyguy'),
(1, 100, '<img src=img/ranks/koopa.gif width=16 height=27><br>Koopa'),
(1, 120, '<img src=img/ranks/redkoopa.gif width=16 height=27><br>Red Koopa'),
(1, 140, '<img src=img/ranks/paratroopa.gif width=16 height=28><br>Paratroopa'),
(1, 160, '<img src=img/ranks/redparatroopa.gif width=16 height=28><br>Red Paratroopa'),
(1, 180, '<img src=img/ranks/cheepcheep.gif width=16 height=16><br>Cheep-cheep'),
(1, 200, '<img src=img/ranks/redcheepcheep.gif width=16 height=16><br>Red Cheep-cheep'),
(1, 225, '<img src=img/ranks/ninji.gif width=16 height=16><br>Ninji'),
(1, 250, '<img src=img/ranks/flurry.gif width=16 height=16><br>Flurry'),
(1, 275, '<img src=img/ranks/snifit.gif width=16 height=16><br>Snifit'),
(1, 300, '<img src=img/ranks/porcupo.gif width=16 height=16><br>Porcupo'),
(1, 325, '<img src=img/ranks/panser.gif width=16 height=16><br>Panser'),
(1, 350, '<img src=img/ranks/mole.gif width=16 height=16><br>Mole'),
(1, 375, '<img src=img/ranks/beetle.gif width=16 height=16><br>Buzzy Beetle'),
(1, 400, '<img src=img/ranks/nipperplant.gif width=16 height=16><br>Nipper Plant'),
(1, 425, '<img src=img/ranks/bloober.gif width=16 height=16><br>Bloober'),
(1, 450, '<img src=img/ranks/busterbeetle.gif width=16 height=15><br>Buster Beetle'),
(1, 475, '<img src=img/ranks/beezo.gif width=16 height=16><br>Beezo'),
(1, 500, '<img src=img/ranks/bulletbill.gif width=16 height=14><br>Bullet Bill'),
(1, 525, '<img src=img/ranks/rex.gif width=20 height=32><br>Rex'),
(1, 550, '<img src=img/ranks/lakitu.gif width=16 height=24><br>Lakitu'),
(1, 575, '<img src=img/ranks/spiny.gif width=16 height=16><br>Spiny'),
(1, 600, '<img src=img/ranks/bobomb.gif width=16 height=16><br>Bob-Omb'),
(1, 700, '<img src=img/ranks/spike.gif width=32 height=32><br>Spike'),
(1, 675, '<img src=img/ranks/pokey.gif width=18 height=64><br>Pokey'),
(1, 650, '<img src=img/ranks/cobrat.gif width=16 height=32><br>Cobrat'),
(1, 725, '<img src=img/ranks/hedgehog.gif width=16 height=24><br>Melon Bug'),
(1, 750, '<img src=img/ranks/lanternghost.gif width=26 height=19><br>Lantern Ghost'),
(1, 775, '<img src=img/ranks/fuzzy.gif width=32 height=31><br>Fuzzy'),
(1, 800, '<img src=img/ranks/bandit.gif width=23 height=28><br>Bandit'),
(1, 830, '<img src=img/ranks/superkoopa.gif width=23 height=13><br>Super Koopa'),
(1, 860, '<img src=img/ranks/redsuperkoopa.gif width=23 height=13><br>Red Super Koopa'),
(1, 900, '<img src=img/ranks/boo.gif width=16 height=16><br>Boo'),
(1, 925, '<img src=img/ranks/boo2.gif width=16 height=16><br>Boo'),
(1, 950, '<img src=img/ranks/fuzzball.gif width=16 height=16><br>Fuzz Ball'),
(1, 1000, '<img src=img/ranks/boomerangbrother.gif width=60 height=40><br>Boomerang Brother'),
(1, 1050, '<img src=img/ranks/hammerbrother.gif width=60 height=40><br>Hammer Brother'),
(1, 1100, '<img src=img/ranks/firebrother.gif width=60 height=24><br>Fire Brother'),
(1, 1150, '<img src=img/ranks/firesnake.gif width=45 height=36><br>Fire Snake'),
(1, 1200, '<img src=img/ranks/giantgoomba.gif width=24 height=23><br>Giant Goomba'),
(1, 1250, '<img src=img/ranks/giantkoopa.gif width=24 height=31><br>Giant Koopa'),
(1, 1300, '<img src=img/ranks/giantredkoopa.gif width=24 height=31><br>Giant Red Koopa'),
(1, 1350, '<img src=img/ranks/giantparatroopa.gif width=24 height=31><br>Giant Paratroopa'),
(1, 1400, '<img src=img/ranks/giantredparatroopa.gif width=24 height=31><br>Giant Red Paratroopa'),
(1, 1450, '<img src=img/ranks/chuck.gif width=28 height=27><br>Chuck'),
(1, 1500, '<img src=img/ranks/thwomp.gif width=44 height=32><br>Thwomp'),
(1, 1550, '<img src=img/ranks/bigcheepcheep.gif width=24 height=32><br>Boss Bass'),
(1, 1600, '<img src=img/ranks/volcanolotus.gif width=32 height=30><br>Volcano Lotus'),
(1, 1650, '<img src=img/ranks/lavalotus.gif width=24 height=32><br>Lava Lotus'),
(1, 1700, '<img src=img/ranks/ptooie2.gif width=16 height=43><br>Ptooie'),
(1, 1800, '<img src=img/ranks/sledgebrother.gif width=60 height=50><br>Sledge Brother'),
(1, 1900, '<img src=img/ranks/boomboom.gif width=28 height=26><br>Boomboom'),
(1, 2000, '<img src=img/ranks/birdopink.gif width=60 height=36><br>Birdo'),
(1, 2100, '<img src=img/ranks/birdored.gif width=60 height=36><br>Red Birdo'),
(1, 2200, '<img src=img/ranks/birdogreen.gif width=60 height=36><br>Green Birdo'),
(1, 2300, '<img src=img/ranks/iggy.gif width=28><br>Larry Koopa'),
(1, 2400, '<img src=img/ranks/morton.gif width=34><br>Morton Koopa'),
(1, 2500, '<img src=img/ranks/wendy.gif width=28><br>Wendy Koopa'),
(1, 2600, '<img src=img/ranks/larry.gif width=28><br>Iggy Koopa'),
(1, 2700, '<img src=img/ranks/roy.gif width=34><br>Roy Koopa'),
(1, 2800, '<img src=img/ranks/lemmy.gif width=28><br>Lemmy Koopa'),
(1, 2900, '<img src=img/ranks/ludwig.gif width=33><br>Ludwig Von Koopa'),
(1, 3000, '<img src=img/ranks/triclyde.gif width=40 height=48><br>Triclyde'),
(1, 3100, '<img src=img/ranks/kamek.gif width=45 height=34><br>Magikoopa'),
(1, 3200, '<img src=img/ranks/wart.gif width=40 height=47><br>Wart'),
(1, 3300, '<img src=img/ranks/babybowser.gif width=36 height=36><br>Baby Bowser'),
(1, 3400, '<img src=img/ranks/bowser.gif width=52 height=49><br>King Bowser Koopa'),
(1, 3500, '<img src=img/ranks/yoshi.gif width=31 height=33><br>Yoshi'),
(1, 3600, '<img src=img/ranks/yoshiyellow.gif width=31 height=32><br>Yellow Yoshi'),
(1, 3700, '<img src=img/ranks/yoshiblue.gif width=36 height=35><br>Blue Yoshi'),
(1, 3800, '<img src=img/ranks/yoshired.gif width=33 height=36><br>Red Yoshi'),
(1, 3900, '<img src=img/ranks/kingyoshi.gif width=24 height=34><br>King Yoshi'),
(1, 4000, '<img src=img/ranks/babymario.gif width=28 height=24><br>Baby Mario'),
(1, 4100, '<img src=img/ranks/luigismall.gif width=15 height=22><br>Luigi'),
(1, 4200, '<img src=img/ranks/mariosmall.gif width=15 height=20><br>Mario'),
(1, 4300, '<img src=img/ranks/luigibig.gif width=16 height=30><br>Super Luigi'),
(1, 4400, '<img src=img/ranks/mariobig.gif width=16 height=28><br>Super Mario'),
(1, 4500, '<img src=img/ranks/luigifire.gif width=16 height=30><br>Fire Luigi'),
(1, 4600, '<img src=img/ranks/mariofire.gif width=16 height=28><br>Fire Mario'),
(1, 4700, '<img src=img/ranks/luigicape.gif width=26 height=30><br>Cape Luigi'),
(1, 4800, '<img src=img/ranks/mariocape.gif width=26 height=28><br>Cape Mario'),
(1, 4900, '<img src=img/ranks/luigistar.gif width=16 height=30><br>Star Luigi'),
(1, 5000, '<img src=img/ranks/mariostar.gif width=16 height=28><br>Star Mario'),
(1, 625, '<img src=img/ranks/drybones.gif><br>Dry Bones'),
(1, 10000, 'Climbing the ranks again!'),
(3, 0, 'Non-poster'),
(3, 1, '<img src=img/ranksk/kirbystrut.gif><br/>Struttin\' On In'),
(3, 10, '<img src=img/ranksk/waddledee.gif><br/>Waddle Dee'),
(3, 20, '<img src=img/ranksk/brontoburt.gif><br/>Bronto Burt'),
(3, 35, '<img src=img/ranksk/gator.gif><br/>Gator'),
(3, 50, '<img src=img/ranksk/kabu.gif><br/>Kabu'),
(3, 65, '<img src=img/ranksk/mumbies.gif><br/>Mumbies'),
(3, 80, '<img src=img/ranksk/grizzo.gif><br/>Grizzo'),
(3, 100, '<img src=img/ranksk/tweet.gif><br/>Tweet'),
(3, 110, '<img src=img/ranksk/ufo.gif><br/>UFO'),
(3, 120, '<img src=img/ranksk/tooky.gif><br/>Tooky'),
(3, 140, '<img src=img/ranksk/kruff.gif><br/>Kruff'),
(3, 160, '<img src=img/ranksk/poppybros.gif><br/>Poppy Bros. Jr'),
(3, 180, '<img src=img/ranksk/coney.gif><br/>Coney'),
(3, 200, '<img src=img/ranksk/blipper.gif><br/>Blipper'),
(3, 220, '<img src=img/ranksk/cappy.gif><br/>Cappy'),
(3, 240, '<img src=img/ranksk/glunk.gif><br/>Glunk'),
(3, 260, '<img src=img/ranksk/gungun.gif><br/>Gungun'),
(3, 280, '<img src=img/ranksk/bouncy.gif><br/>Bouncy'),
(3, 300, '<img src=img/ranksk/broomhatter.gif<br/>Broom Hatter'),
(3, 325, '<img src=img/ranksk/squishy.gif><br/>Squishy'),
(3, 350, '<img src=img/ranksk/gordo.gif><br/>Gordo'),
(3, 375, '<img src=img/ranksk/scarfy.gif><br/>Scarfy'),
(3, 400, '<img src=img/ranksk/scarfyprovoked.gif><br/>Provoked Scarfy'),
(3, 425, '<img src=img/ranksk/simirror.gif><br/>Simirror'),
(3, 450, '<img src=img/ranksk/noddy.gif><br/>Noddy'),
(3, 475, '<img src=img/ranksk/capsulej.gif><br/>Capsule-J'),
(3, 500, '<img src=img/ranksk/walkie.gif><br/>Walkie'),
(3, 525, '<img src=img/ranksk/waddledoo.gif><br/>Waddle Doo'),
(3, 550, '<img src=img/ranksk/togezo.gif><br/>Togezo'),
(3, 575, '<img src=img/ranksk/knucklejoe.gif><br/>Knuckle Joe'),
(3, 600, '<img src=img/ranksk/sirkibble.gif><br/>Sir Kibble'),
(3, 645, '<img src=img/ranksk/bomber.gif><br/>Bomber'),
(3, 650, '<img src=img/ranksk/plasmawhisp.gif><br/>Plasma Whisp'),
(3, 675, '<img src=img/ranksk/twister.gif><br/>Twister'),
(3, 700, '<img src=img/ranksk/bobo.gif><br/>Bobo'),
(3, 725, '<img src=img/ranksk/sparky.gif><br/>Sparky'),
(3, 750, '<img src=img/ranksk/chilly.gif><br/>Chilly'),
(3, 775, '<img src=img/ranksk/biospark.gif><br/>Bio Spark'),
(3, 800, '<img src=img/ranksk/burninleo.gif><br/>Burnin\' Leo'),
(3, 830, '<img src=img/ranksk/gim.gif><br/>Gim'),
(3, 860, '<img src=img/ranksk/rocky.gif><br/>Rocky'),
(3, 900, '<img src=img/ranksk/parasolwdee.gif><br/>Parasol W. Dee'),
(3, 925, '<img src=img/ranksk/tac.gif><br/>T.A.C'),
(3, 950, '<img src=img/ranksk/birdon.gif><br/>Birdon'),
(3, 1000, '<img src=img/ranksk/bladeknight.gif><br/>Blade Knight'),
(3, 1050, '<img src=img/ranksk/bonkers.gif><br/>Bonkers'),
(3, 1150, '<img src=img/ranksk/giantwaddledee.gif><br/>Giant Waddle Dee'),
(3, 1200, '<img src=img/ranksk/tosstortoise.gif><br/>Toss Tortoise'),
(3, 1250, '<img src=img/ranksk/haboki.gif><br/>Haboki'),
(3, 1300, '<img src=img/ranksk/phanphan.gif><br/>Phan Phan'),
(3, 1350, '<img src=img/ranksk/wheeliebig.gif><br/>Big Wheelie'),
(3, 1400, '<img src=img/ranksk/clockwork.gif><br/>Clockwork'),
(3, 1450, '<img src=img/ranksk/chefkawasaki.gif><br/>Chef Kawasaki'),
(3, 1500, '<img src=img/ranksk/poppybrossr.gif><br/>Poppy Bros. Sr'),
(3, 1550, '<img src=img/ranksk/frosty.gif><br/>Frosty'),
(3, 1600, '<img src=img/ranksk/jumpershoot.gif><br/>Jumper Shoot'),
(3, 1650, '<img src=img/ranksk/flamemane.gif><br/>Flame Mane'),
(3, 1700, '<img src=img/ranksk/karateman.gif><br/>Karate Man'),
(3, 1800, '<img src=img/ranksk/bugzzy.gif><br/>Bugzzy'),
(3, 1900, '<img src=img/ranksk/captainstitch.gif><br/>Captain Stitch'),
(3, 2000, '<img src=img/ranksk/masterhand.gif><br/>Master Hand'),
(3, 2100, '<img src=img/ranksk/boboo.gif><br/>Boboo'),
(3, 2200, '<img src=img/ranksk/whispywoods.gif><br/>Whispy Woods'),
(3, 2300, '<img src=img/ranksk/paintroller.gif><br/>Paint Roller'),
(3, 2400, '<img src=img/ranksk/kracko.gif><br/>Kracko'),
(3, 2500, '<img src=img/ranksk/kirbybreak.gif><br/>Halfway There'),
(3, 2600, '<img src=img/ranksk/mrshine.gif><br/>Mr. Shine'),
(3, 2700, '<img src=img/ranksk/mrbright.gif><br/>Mr. Bright'),
(3, 2800, '<img src=img/ranksk/masher.gif><br/>Masher'),
(3, 2900, '<img src=img/ranksk/butch.gif><br/>Butch'),
(3, 3000, '<img src=img/ranksk/stave.gif><br/>Stave'),
(3, 3100, '<img src=img/ranksk/lunarknight.gif><br/>Lunar Knight'),
(3, 3200, '<img src=img/ranksk/metaknight.gif><br/>Meta Knight'),
(3, 3300, '<img src=img/ranksk/acro.gif><br/>Acro'),
(3, 3400, '<img src=img/ranksk/starorb.gif><br/>Star Orb'),
(3, 3500, '<img src=img/ranksk/darkmatter.gif><br/>Dark Matter'),
(3, 3600, '<img src=img/ranksk/kingdedede.gif><br/>King Dedede'),
(3, 3700, '<img src=img/ranksk/dynababy.gif><br/>Dyna Baby'),
(3, 3800, '<img src=img/ranksk/gooey.gif><br/>Gooey'),
(3, 3900, '<img src=img/ranksk/kine.gif><br/>Kine'),
(3, 4000, '<img src=img/ranksk/coo.gif><br/>Coo'),
(3, 4100, '<img src=img/ranksk/rick.gif><br/>Rick'),
(3, 4200, '<img src=img/ranksk/kirbyplush.gif><br/>Kirby Plush'),
(3, 4300, '<img src=img/ranksk/kirbysamurai.gif><br/>Samurai Kirby'),
(3, 4400, '<img src=img/ranksk/kirbydreamland3.gif><br/>Swimming Kirby'),
(3, 4500, '<img src=img/ranksk/kirbyadventure.gif><br/>Floating Kirby'),
(3, 4600, '<img src=img/ranksk/wheelierider.gif><br/>Wheelie Rider'),
(3, 4700, '<img src=img/ranksk/kirbywarpstar.gif><br/>Warpstar Kirby'),
(3, 4800, '<img src=img/ranksk/kirbyship.gif><br/>Kirby Ship'),
(3, 4900, '<img src=img/ranksk/panic.gif><br/>Panic!'),
(3, 5000, '<img src=img/ranksk/kirbysnooze.gif><br/>Good Work'),
(4, 0, 'Newcomer'),
(4, 10, '<img src=img/rankss/ring.gif>'),
(4, 50, '<img src=img/rankss/ce-blue.png>'),
(4, 80, '<img src=img/rankss/ce-purple.png>'),
(4, 110, '<img src=img/rankss/ce-red.png>'),
(4, 140, '<img src=img/rankss/ce-pink.png>'),
(4, 170, '<img src=img/rankss/ce-yellow.png>'),
(4, 200, '<img src=img/rankss/ce-green.png>'),
(4, 230, '<img src=img/rankss/ce-grey.png>'),
(4, 260, '<img src=img/rankss/se-blue.png>'),
(4, 350, '<img src=img/rankss/se-purple.png>'),
(4, 440, '<img src=img/rankss/se-red.png>'),
(4, 530, '<img src=img/rankss/se-pink.png>'),
(4, 620, '<img src=img/rankss/se-yellow.png>'),
(4, 710, '<img src=img/rankss/se-green.png>'),
(4, 800, '<img src=img/rankss/se-grey.png>'),
(4, 1000, '<img src=img/rankss/master.png>');

-- --------------------------------------------------------

--
-- Tabellstruktur `ranksets`
--

CREATE TABLE `ranksets` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `ranksets`
--

INSERT INTO `ranksets` (`id`, `name`) VALUES
(1, 'Mario'),
(0, 'None'),
(-1, 'Dots (by Xkeeper)'),
(3, 'Kirby (by YoshiDude)'),
(4, 'Sonic (by Danika)');

-- --------------------------------------------------------

--
-- Tabellstruktur `ref`
--

CREATE TABLE `ref` (
  `time` int(11) NOT NULL,
  `urlfrom` varchar(255) NOT NULL,
  `urlto` varchar(255) NOT NULL,
  `userid` int(11) NOT NULL,
  `ipaddr` varchar(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `ref`
--

INSERT INTO `ref` (`time`, `urlfrom`, `urlto`, `userid`, `ipaddr`) VALUES
(1507992264, 'http://127.0.0.1/Acmlmboard/', '/Acmlmboard/index.php', 0, '127.0.0.1'),
(1507992265, 'http://127.0.0.1/Acmlmboard/', '/Acmlmboard/index.php', 0, '127.0.0.1'),
(1507993126, 'http://127.0.0.1/', '/Acmlmboard/index.php', 0, '127.0.0.1'),
(1507993258, 'http://127.0.0.1/', '/Acmlmboard/index.php', 0, '127.0.0.1'),
(1507993362, 'http://127.0.0.1/Acmlmboard/', '/Acmlmboard/login.php', 0, '127.0.0.1'),
(1507993365, 'http://127.0.0.1/Acmlmboard/login.php', '/Acmlmboard/index.php', 0, '127.0.0.1'),
(1507993366, 'http://127.0.0.1/Acmlmboard/', '/Acmlmboard/faq.php', 0, '127.0.0.1'),
(1507993381, 'http://127.0.0.1/Acmlmboard/', '/Acmlmboard/index.php', 0, '127.0.0.1'),
(1507993848, 'http://127.0.0.1/Acmlmboard/', '/Acmlmboard/index.php', 0, '127.0.0.1'),
(1507994025, 'http://127.0.0.1/Acmlmboard/', '/Acmlmboard/index.php', 0, '127.0.0.1'),
(1507994168, 'http://127.0.0.1/Acmlmboard/', '/Acmlmboard/index.php', 0, '127.0.0.1'),
(1507994170, 'http://127.0.0.1/Acmlmboard/', '/Acmlmboard/activeusers.php', 0, '127.0.0.1'),
(1507994174, 'http://127.0.0.1/Acmlmboard/activeusers.php', '/Acmlmboard/index.php', 0, '127.0.0.1'),
(1507994195, 'http://127.0.0.1/Acmlmboard/', '/Acmlmboard/register.php', 0, '127.0.0.1'),
(1507994239, 'http://127.0.0.1/Acmlmboard/register.php', '/Acmlmboard/register.php', 0, '127.0.0.1'),
(1507994275, 'http://127.0.0.1/Acmlmboard/register.php', '/Acmlmboard/register.php', 0, '127.0.0.1'),
(1507994275, 'http://127.0.0.1/Acmlmboard/register.php', '/Acmlmboard/login.php', 0, '127.0.0.1'),
(1507994278, 'http://127.0.0.1/Acmlmboard/login.php', '/Acmlmboard/index.php', 0, '127.0.0.1'),
(1507994284, 'http://127.0.0.1/Acmlmboard/', '/Acmlmboard/profile.php?id=', 0, '127.0.0.1'),
(1507994286, 'http://127.0.0.1/Acmlmboard/profile.php?id=', '/Acmlmboard/login.php', 0, '127.0.0.1'),
(1507994310, 'http://127.0.0.1/Acmlmboard/login.php', '/Acmlmboard/login.php', 0, '127.0.0.1'),
(1507994311, 'http://127.0.0.1/Acmlmboard/login.php', '/Acmlmboard/index.php', 1, '127.0.0.1'),
(1507994319, 'http://127.0.0.1/Acmlmboard/', '/Acmlmboard/editprofile.php', 1, '127.0.0.1'),
(1507995185, 'http://127.0.0.1/Acmlmboard/editprofile.php', '/Acmlmboard/index.php', 1, '127.0.0.1'),
(1507996511, 'http://127.0.0.1/Acmlmboard/', '/Acmlmboard/faq.php', 1, '127.0.0.1'),
(1507996549, 'http://127.0.0.1/Acmlmboard/faq.php', '/Acmlmboard/index.php', 1, '127.0.0.1'),
(1507998183, 'http://127.0.0.1/Acmlmboard/faq.php', '/Acmlmboard/index.php', 1, '127.0.0.1'),
(1507998186, 'http://127.0.0.1/Acmlmboard/', '/Acmlmboard/profile.php?id=1', 1, '127.0.0.1'),
(1507998188, 'http://127.0.0.1/Acmlmboard/profile.php?id=1', '/Acmlmboard/index.php', 1, '127.0.0.1'),
(1507998191, 'http://127.0.0.1/Acmlmboard/', '/Acmlmboard/faq.php', 1, '127.0.0.1'),
(1507998192, 'http://127.0.0.1/Acmlmboard/faq.php', '/Acmlmboard/irc.php', 1, '127.0.0.1'),
(1507998193, 'http://127.0.0.1/Acmlmboard/irc.php', '/Acmlmboard/memberlist.php', 1, '127.0.0.1'),
(1507998194, 'http://127.0.0.1/Acmlmboard/memberlist.php', '/Acmlmboard/activeusers.php', 1, '127.0.0.1'),
(1507998199, 'http://127.0.0.1/Acmlmboard/activeusers.php', '/Acmlmboard/login.php', 1, '127.0.0.1'),
(1507998199, 'http://127.0.0.1/Acmlmboard/activeusers.php', '/Acmlmboard/index.php', 0, '127.0.0.1');

-- --------------------------------------------------------

--
-- Tabellstruktur `resetpass`
--

CREATE TABLE `resetpass` (
  `id` int(11) UNSIGNED NOT NULL,
  `user` mediumint(9) UNSIGNED NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL DEFAULT '0',
  `ip` char(15) NOT NULL,
  `token` varchar(32) NOT NULL,
  `oldpass` varchar(32) DEFAULT NULL,
  `newpass` varchar(32) DEFAULT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `robots`
--

CREATE TABLE `robots` (
  `bot_name` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `bot_agent` varchar(100) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `rpgchat`
--

CREATE TABLE `rpgchat` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `chan` tinyint(4) NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL DEFAULT '0',
  `user` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `text` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `rpgrooms`
--

CREATE TABLE `rpgrooms` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `lvmin` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `lvmax` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `users` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `usermax` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `title` varchar(32) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `turn` mediumint(8) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `smilies`
--

CREATE TABLE `smilies` (
  `text` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `smilies`
--

INSERT INTO `smilies` (`text`, `url`) VALUES
('-_-', 'img/smilies/annoyed.gif'),
('~:o', 'img/smilies/baby.gif'),
('o_O', 'img/smilies/bigeyes.gif'),
(':D', 'img/smilies/biggrin.gif'),
('o_o', 'img/smilies/blank.gif'),
(';_;', 'img/smilies/cry.gif'),
('^^;;;', 'img/smilies/cute2.gif'),
('^_^', 'img/smilies/cute.gif'),
('@_@', 'img/smilies/dizzy.gif'),
('O_O', 'img/smilies/eek.gif'),
('>:]', 'img/smilies/evil.gif'),
(':eyeshift:', 'img/smilies/eyeshift.gif'),
(':(', 'img/smilies/frown.gif'),
('8-)', 'img/smilies/glasses.gif'),
(':LOL:', 'img/smilies/lol.gif'),
('>:[', 'img/smilies/mad.gif'),
('<_<', 'img/smilies/shiftleft.gif'),
('>_>', 'img/smilies/shiftright.gif'),
('x_x', 'img/smilies/sick.gif'),
(':)', 'img/smilies/smile.gif'),
(':P', 'img/smilies/tongue.gif'),
(':B', 'img/smilies/vamp.gif'),
(';)', 'img/smilies/wink.gif'),
(':S', 'img/smilies/wobbly.gif'),
('>_<', 'img/smilies/yuck.gif'),
(':yes:', 'img/smilies/yes.png'),
(':no:', 'img/smilies/no.png'),
(':heart:', 'img/smilies/heart.gif'),
('w00t', 'img/smilies/woot.gif'),
(':x', 'img/smilies/crossmouth.gif'),
(':|', 'img/smilies/slidemouth.gif'),
(':@', 'img/smilies/dropsmile.gif'),
(':-3', 'img/smilies/wobble.gif'),
('X-P', 'img/smilies/xp.gif'),
('X-3', 'img/smilies/x3.gif'),
('X-D', 'img/smilies/xd.gif'),
(':o', 'img/smilies/dramatic.gif');

-- --------------------------------------------------------

--
-- Tabellstruktur `spambotlog`
--

CREATE TABLE `spambotlog` (
  `ip` varchar(15) NOT NULL,
  `user` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `title` text NOT NULL,
  `text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `spritecateg`
--

CREATE TABLE `spritecateg` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `spritecateg`
--

INSERT INTO `spritecateg` (`id`, `name`) VALUES
(0, 'Miscellaneous/Unclassified'),
(1, 'Super Mario Brothers Series'),
(2, 'Legend of Zelda Series'),
(3, 'Metroid Series'),
(4, 'Pok&#233;mon Series'),
(5, 'Kirby Series'),
(6, 'Legend of the Evil PLACEHOLDER'),
(7, 'Sonic the Hedgehog Series');

-- --------------------------------------------------------

--
-- Tabellstruktur `sprites`
--

CREATE TABLE `sprites` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `franchiseid` int(11) NOT NULL DEFAULT '0',
  `pic` varchar(256) NOT NULL,
  `alt` varchar(256) NOT NULL,
  `anchor` enum('free','left','right','top','bottom','sides','sidepic') NOT NULL,
  `title` varchar(256) NOT NULL,
  `flavor` text NOT NULL,
  `rarity` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `sprite_captures`
--

CREATE TABLE `sprite_captures` (
  `userid` int(11) NOT NULL,
  `monid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `tags`
--

CREATE TABLE `tags` (
  `bit` int(8) NOT NULL,
  `fid` int(10) NOT NULL,
  `name` varchar(64) NOT NULL,
  `tag` varchar(20) NOT NULL,
  `color` varchar(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `threads`
--

CREATE TABLE `threads` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `replies` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `views` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `closed` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `sticky` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `forum` int(5) NOT NULL DEFAULT '0',
  `user` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `lastdate` int(11) NOT NULL DEFAULT '0',
  `lastuser` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `lastid` int(11) NOT NULL DEFAULT '0',
  `icon` varchar(100) NOT NULL,
  `tags` int(12) NOT NULL,
  `announce` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `threadsread`
--

CREATE TABLE `threadsread` (
  `uid` mediumint(9) NOT NULL,
  `tid` mediumint(9) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `threadthumbs`
--

CREATE TABLE `threadthumbs` (
  `uid` int(11) NOT NULL,
  `tid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellstruktur `timezones`
--

CREATE TABLE `timezones` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  `offset` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumpning av Data i tabell `timezones`
--

INSERT INTO `timezones` (`id`, `name`, `offset`) VALUES
(1, 'UTC', 0),
(2, 'Africa/Abidjan', 0),
(3, 'Africa/Accra', 0),
(4, 'Africa/Addis_Ababa', 10800),
(5, 'Africa/Algiers', 3600),
(6, 'Africa/Asmara', 10800),
(7, 'Africa/Asmera', 10800),
(8, 'Africa/Bamako', 0),
(9, 'Africa/Bangui', 3600),
(10, 'Africa/Banjul', 0),
(11, 'Africa/Bissau', 0),
(12, 'Africa/Blantyre', 7200),
(13, 'Africa/Brazzaville', 3600),
(14, 'Africa/Bujumbura', 7200),
(15, 'Africa/Cairo', 7200),
(16, 'Africa/Casablanca', 0),
(17, 'Africa/Ceuta', 3600),
(18, 'Africa/Conakry', 0),
(19, 'Africa/Dakar', 0),
(20, 'Africa/Dar_es_Salaam', 10800),
(21, 'Africa/Djibouti', 10800),
(22, 'Africa/Douala', 3600),
(23, 'Africa/El_Aaiun', 0),
(24, 'Africa/Freetown', 0),
(25, 'Africa/Gaborone', 7200),
(26, 'Africa/Harare', 7200),
(27, 'Africa/Johannesburg', 7200),
(28, 'Africa/Juba', 0),
(29, 'Africa/Kampala', 0),
(30, 'Africa/Khartoum', 0),
(31, 'Africa/Kigali', 0),
(32, 'Africa/Kinshasa', 0),
(33, 'Africa/Lagos', 0),
(34, 'Africa/Libreville', 0),
(35, 'Africa/Lome', 0),
(36, 'Africa/Luanda', 0),
(37, 'Africa/Lubumbashi', 0),
(38, 'Africa/Lusaka', 0),
(39, 'Africa/Malabo', 0),
(40, 'Africa/Maputo', 0),
(41, 'Africa/Maseru', 0),
(42, 'Africa/Mbabane', 0),
(43, 'Africa/Mogadishu', 0),
(44, 'Africa/Monrovia', 0),
(45, 'Africa/Nairobi', 0),
(46, 'Africa/Ndjamena', 0),
(47, 'Africa/Niamey', 0),
(48, 'Africa/Nouakchott', 0),
(49, 'Africa/Ouagadougou', 0),
(50, 'Africa/Porto-Novo', 0),
(51, 'Africa/Sao_Tome', 0),
(52, 'Africa/Timbuktu', 0),
(53, 'Africa/Tripoli', 0),
(54, 'Africa/Tunis', 0),
(55, 'Africa/Windhoek', 0),
(56, 'America/Adak', 0),
(57, 'America/Anchorage', 0),
(58, 'America/Anguilla', 0),
(59, 'America/Antigua', 0),
(60, 'America/Araguaina', 0),
(61, 'America/Argentina/Buenos_Aires', 0),
(62, 'America/Argentina/Catamarca', 0),
(63, 'America/Argentina/ComodRivadavia', 0),
(64, 'America/Argentina/Cordoba', 0),
(65, 'America/Argentina/Jujuy', 0),
(66, 'America/Argentina/La_Rioja', 0),
(67, 'America/Argentina/Mendoza', 0),
(68, 'America/Argentina/Rio_Gallegos', 0),
(69, 'America/Argentina/Salta', 0),
(70, 'America/Argentina/San_Juan', 0),
(71, 'America/Argentina/San_Luis', 0),
(72, 'America/Argentina/Tucuman', 0),
(73, 'America/Argentina/Ushuaia', 0),
(74, 'America/Aruba', 0),
(75, 'America/Asuncion', 0),
(76, 'America/Atikokan', 0),
(77, 'America/Atka', 0),
(78, 'America/Bahia', 0),
(79, 'America/Bahia_Banderas', 0),
(80, 'America/Barbados', 0),
(81, 'America/Belem', 0),
(82, 'America/Belize', 0),
(83, 'America/Blanc-Sablon', 0),
(84, 'America/Boa_Vista', 0),
(85, 'America/Bogota', 0),
(86, 'America/Boise', 0),
(87, 'America/Buenos_Aires', 0),
(88, 'America/Cambridge_Bay', 0),
(89, 'America/Campo_Grande', 0),
(90, 'America/Cancun', 0),
(91, 'America/Caracas', 0),
(92, 'America/Catamarca', 0),
(93, 'America/Cayenne', 0),
(94, 'America/Cayman', 0),
(95, 'America/Chicago', 0),
(96, 'America/Chihuahua', 0),
(97, 'America/Coral_Harbour', 0),
(98, 'America/Cordoba', 0),
(99, 'America/Costa_Rica', 0),
(100, 'America/Cuiaba', 0),
(101, 'America/Curacao', 0),
(102, 'America/Danmarkshavn', 0),
(103, 'America/Dawson', 0),
(104, 'America/Dawson_Creek', 0),
(105, 'America/Denver', 0),
(106, 'America/Detroit', 0),
(107, 'America/Dominica', 0),
(108, 'America/Edmonton', 0),
(109, 'America/Eirunepe', 0),
(110, 'America/El_Salvador', 0),
(111, 'America/Ensenada', 0),
(112, 'America/Fort_Wayne', 0),
(113, 'America/Fortaleza', 0),
(114, 'America/Glace_Bay', 0),
(115, 'America/Godthab', 0),
(116, 'America/Goose_Bay', 0),
(117, 'America/Grand_Turk', 0),
(118, 'America/Grenada', 0),
(119, 'America/Guadeloupe', 0),
(120, 'America/Guatemala', 0),
(121, 'America/Guayaquil', 0),
(122, 'America/Guyana', 0),
(123, 'America/Halifax', 0),
(124, 'America/Havana', 0),
(125, 'America/Hermosillo', 0),
(126, 'America/Indiana/Indianapolis', 0),
(127, 'America/Indiana/Knox', 0),
(128, 'America/Indiana/Marengo', 0),
(129, 'America/Indiana/Petersburg', 0),
(130, 'America/Indiana/Tell_City', 0),
(131, 'America/Indiana/Vevay', 0),
(132, 'America/Indiana/Vincennes', 0),
(133, 'America/Indiana/Winamac', 0),
(134, 'America/Indianapolis', 0),
(135, 'America/Inuvik', 0),
(136, 'America/Iqaluit', 0),
(137, 'America/Jamaica', 0),
(138, 'America/Jujuy', 0),
(139, 'America/Juneau', 0),
(140, 'America/Kentucky/Louisville', 0),
(141, 'America/Kentucky/Monticello', 0),
(142, 'America/Knox_IN', 0),
(143, 'America/Kralendijk', 0),
(144, 'America/La_Paz', 0),
(145, 'America/Lima', 0),
(146, 'America/Los_Angeles', 0),
(147, 'America/Louisville', 0),
(148, 'America/Lower_Princes', 0),
(149, 'America/Maceio', 0),
(150, 'America/Managua', 0),
(151, 'America/Manaus', 0),
(152, 'America/Marigot', 0),
(153, 'America/Martinique', 0),
(154, 'America/Matamoros', 0),
(155, 'America/Mazatlan', 0),
(156, 'America/Mendoza', 0),
(157, 'America/Menominee', 0),
(158, 'America/Merida', 0),
(159, 'America/Metlakatla', 0),
(160, 'America/Mexico_City', 0),
(161, 'America/Miquelon', 0),
(162, 'America/Moncton', 0),
(163, 'America/Monterrey', 0),
(164, 'America/Montevideo', 0),
(165, 'America/Montreal', 0),
(166, 'America/Montserrat', 0),
(167, 'America/Nassau', 0),
(168, 'America/New_York', 0),
(169, 'America/Nipigon', 0),
(170, 'America/Nome', 0),
(171, 'America/Noronha', 0),
(172, 'America/North_Dakota/Beulah', 0),
(173, 'America/North_Dakota/Center', 0),
(174, 'America/North_Dakota/New_Salem', 0),
(175, 'America/Ojinaga', 0),
(176, 'America/Panama', 0),
(177, 'America/Pangnirtung', 0),
(178, 'America/Paramaribo', 0),
(179, 'America/Phoenix', 0),
(180, 'America/Port-au-Prince', 0),
(181, 'America/Port_of_Spain', 0),
(182, 'America/Porto_Acre', 0),
(183, 'America/Porto_Velho', 0),
(184, 'America/Puerto_Rico', 0),
(185, 'America/Rainy_River', 0),
(186, 'America/Rankin_Inlet', 0),
(187, 'America/Recife', 0),
(188, 'America/Regina', 0),
(189, 'America/Resolute', 0),
(190, 'America/Rio_Branco', 0),
(191, 'America/Rosario', 0),
(192, 'America/Santa_Isabel', 0),
(193, 'America/Santarem', 0),
(194, 'America/Santiago', 0),
(195, 'America/Santo_Domingo', 0),
(196, 'America/Sao_Paulo', 0),
(197, 'America/Scoresbysund', 0),
(198, 'America/Shiprock', 0),
(199, 'America/Sitka', 0),
(200, 'America/St_Barthelemy', 0),
(201, 'America/St_Johns', 0),
(202, 'America/St_Kitts', 0),
(203, 'America/St_Lucia', 0),
(204, 'America/St_Thomas', 0),
(205, 'America/St_Vincent', 0),
(206, 'America/Swift_Current', 0),
(207, 'America/Tegucigalpa', 0),
(208, 'America/Thule', 0),
(209, 'America/Thunder_Bay', 0),
(210, 'America/Tijuana', 0),
(211, 'America/Toronto', 0),
(212, 'America/Tortola', 0),
(213, 'America/Vancouver', 0),
(214, 'America/Virgin', 0),
(215, 'America/Whitehorse', 0),
(216, 'America/Winnipeg', 0),
(217, 'America/Yakutat', 0),
(218, 'America/Yellowknife', 0),
(219, 'Antarctica/Casey', 0),
(220, 'Antarctica/Davis', 0),
(221, 'Antarctica/DumontDUrville', 0),
(222, 'Antarctica/Macquarie', 0),
(223, 'Antarctica/Mawson', 0),
(224, 'Antarctica/McMurdo', 0),
(225, 'Antarctica/Palmer', 0),
(226, 'Antarctica/Rothera', 0),
(227, 'Antarctica/South_Pole', 0),
(228, 'Antarctica/Syowa', 0),
(229, 'Antarctica/Vostok', 0),
(230, 'Arctic/Longyearbyen', 0),
(231, 'Asia/Aden', 0),
(232, 'Asia/Almaty', 0),
(233, 'Asia/Amman', 0),
(234, 'Asia/Anadyr', 0),
(235, 'Asia/Aqtau', 0),
(236, 'Asia/Aqtobe', 0),
(237, 'Asia/Ashgabat', 0),
(238, 'Asia/Ashkhabad', 0),
(239, 'Asia/Baghdad', 0),
(240, 'Asia/Bahrain', 0),
(241, 'Asia/Baku', 0),
(242, 'Asia/Bangkok', 0),
(243, 'Asia/Beirut', 0),
(244, 'Asia/Bishkek', 0),
(245, 'Asia/Brunei', 0),
(246, 'Asia/Calcutta', 0),
(247, 'Asia/Choibalsan', 0),
(248, 'Asia/Chongqing', 0),
(249, 'Asia/Chungking', 0),
(250, 'Asia/Colombo', 0),
(251, 'Asia/Dacca', 0),
(252, 'Asia/Damascus', 0),
(253, 'Asia/Dhaka', 0),
(254, 'Asia/Dili', 0),
(255, 'Asia/Dubai', 0),
(256, 'Asia/Dushanbe', 0),
(257, 'Asia/Gaza', 0),
(258, 'Asia/Harbin', 0),
(259, 'Asia/Hebron', 0),
(260, 'Asia/Ho_Chi_Minh', 0),
(261, 'Asia/Hong_Kong', 0),
(262, 'Asia/Hovd', 0),
(263, 'Asia/Irkutsk', 0),
(264, 'Asia/Istanbul', 0),
(265, 'Asia/Jakarta', 0),
(266, 'Asia/Jayapura', 0),
(267, 'Asia/Jerusalem', 0),
(268, 'Asia/Kabul', 0),
(269, 'Asia/Kamchatka', 0),
(270, 'Asia/Karachi', 0),
(271, 'Asia/Kashgar', 0),
(272, 'Asia/Kathmandu', 0),
(273, 'Asia/Katmandu', 0),
(274, 'Asia/Kolkata', 0),
(275, 'Asia/Krasnoyarsk', 0),
(276, 'Asia/Kuala_Lumpur', 0),
(277, 'Asia/Kuching', 0),
(278, 'Asia/Kuwait', 0),
(279, 'Asia/Macao', 0),
(280, 'Asia/Macau', 0),
(281, 'Asia/Magadan', 0),
(282, 'Asia/Makassar', 0),
(283, 'Asia/Manila', 0),
(284, 'Asia/Muscat', 0),
(285, 'Asia/Nicosia', 0),
(286, 'Asia/Novokuznetsk', 0),
(287, 'Asia/Novosibirsk', 0),
(288, 'Asia/Omsk', 0),
(289, 'Asia/Oral', 0),
(290, 'Asia/Phnom_Penh', 0),
(291, 'Asia/Pontianak', 0),
(292, 'Asia/Pyongyang', 0),
(293, 'Asia/Qatar', 0),
(294, 'Asia/Qyzylorda', 0),
(295, 'Asia/Rangoon', 0),
(296, 'Asia/Riyadh', 0),
(297, 'Asia/Saigon', 0),
(298, 'Asia/Sakhalin', 0),
(299, 'Asia/Samarkand', 0),
(300, 'Asia/Seoul', 0),
(301, 'Asia/Shanghai', 0),
(302, 'Asia/Singapore', 0),
(303, 'Asia/Taipei', 0),
(304, 'Asia/Tashkent', 0),
(305, 'Asia/Tbilisi', 0),
(306, 'Asia/Tehran', 0),
(307, 'Asia/Tel_Aviv', 0),
(308, 'Asia/Thimbu', 0),
(309, 'Asia/Thimphu', 0),
(310, 'Asia/Tokyo', 0),
(311, 'Asia/Ujung_Pandang', 0),
(312, 'Asia/Ulaanbaatar', 0),
(313, 'Asia/Ulan_Bator', 0),
(314, 'Asia/Urumqi', 0),
(315, 'Asia/Vientiane', 0),
(316, 'Asia/Vladivostok', 0),
(317, 'Asia/Yakutsk', 0),
(318, 'Asia/Yekaterinburg', 0),
(319, 'Asia/Yerevan', 0),
(320, 'Atlantic/Azores', 0),
(321, 'Atlantic/Bermuda', 0),
(322, 'Atlantic/Canary', 0),
(323, 'Atlantic/Cape_Verde', 0),
(324, 'Atlantic/Faeroe', 0),
(325, 'Atlantic/Faroe', 0),
(326, 'Atlantic/Jan_Mayen', 0),
(327, 'Atlantic/Madeira', 0),
(328, 'Atlantic/Reykjavik', 0),
(329, 'Atlantic/South_Georgia', 0),
(330, 'Atlantic/St_Helena', 0),
(331, 'Atlantic/Stanley', 0),
(332, 'Australia/ACT', 0),
(333, 'Australia/Adelaide', 0),
(334, 'Australia/Brisbane', 0),
(335, 'Australia/Broken_Hill', 0),
(336, 'Australia/Canberra', 0),
(337, 'Australia/Currie', 0),
(338, 'Australia/Darwin', 0),
(339, 'Australia/Eucla', 0),
(340, 'Australia/Hobart', 0),
(341, 'Australia/LHI', 0),
(342, 'Australia/Lindeman', 0),
(343, 'Australia/Lord_Howe', 0),
(344, 'Australia/Melbourne', 0),
(345, 'Australia/North', 0),
(346, 'Australia/NSW', 0),
(347, 'Australia/Perth', 0),
(348, 'Australia/Queensland', 0),
(349, 'Australia/South', 0),
(350, 'Australia/Sydney', 0),
(351, 'Australia/Tasmania', 0),
(352, 'Australia/Victoria', 0),
(353, 'Australia/West', 0),
(354, 'Australia/Yancowinna', 0),
(355, 'Europe/Amsterdam', 0),
(356, 'Europe/Andorra', 0),
(357, 'Europe/Athens', 0),
(358, 'Europe/Belfast', 0),
(359, 'Europe/Belgrade', 0),
(360, 'Europe/Berlin', 0),
(361, 'Europe/Bratislava', 0),
(362, 'Europe/Brussels', 0),
(363, 'Europe/Bucharest', 0),
(364, 'Europe/Budapest', 0),
(365, 'Europe/Chisinau', 0),
(366, 'Europe/Copenhagen', 0),
(367, 'Europe/Dublin', 0),
(368, 'Europe/Gibraltar', 0),
(369, 'Europe/Guernsey', 0),
(370, 'Europe/Helsinki', 0),
(371, 'Europe/Isle_of_Man', 0),
(372, 'Europe/Istanbul', 0),
(373, 'Europe/Jersey', 0),
(374, 'Europe/Kaliningrad', 0),
(375, 'Europe/Kiev', 0),
(376, 'Europe/Lisbon', 0),
(377, 'Europe/Ljubljana', 0),
(378, 'Europe/London', 0),
(379, 'Europe/Luxembourg', 0),
(380, 'Europe/Madrid', 0),
(381, 'Europe/Malta', 0),
(382, 'Europe/Mariehamn', 0),
(383, 'Europe/Minsk', 0),
(384, 'Europe/Monaco', 0),
(385, 'Europe/Moscow', 0),
(386, 'Europe/Nicosia', 0),
(387, 'Europe/Oslo', 0),
(388, 'Europe/Paris', 0),
(389, 'Europe/Podgorica', 0),
(390, 'Europe/Prague', 0),
(391, 'Europe/Riga', 0),
(392, 'Europe/Rome', 0),
(393, 'Europe/Samara', 0),
(394, 'Europe/San_Marino', 0),
(395, 'Europe/Sarajevo', 0),
(396, 'Europe/Simferopol', 0),
(397, 'Europe/Skopje', 0),
(398, 'Europe/Sofia', 0),
(399, 'Europe/Stockholm', 0),
(400, 'Europe/Tallinn', 0),
(401, 'Europe/Tirane', 0),
(402, 'Europe/Tiraspol', 0),
(403, 'Europe/Uzhgorod', 0),
(404, 'Europe/Vaduz', 0),
(405, 'Europe/Vatican', 0),
(406, 'Europe/Vienna', 0),
(407, 'Europe/Vilnius', 0),
(408, 'Europe/Volgograd', 0),
(409, 'Europe/Warsaw', 0),
(410, 'Europe/Zagreb', 0),
(411, 'Europe/Zaporozhye', 0),
(412, 'Europe/Zurich', 0),
(413, 'Indian/Antananarivo', 0),
(414, 'Indian/Chagos', 0),
(415, 'Indian/Christmas', 0),
(416, 'Indian/Cocos', 0),
(417, 'Indian/Comoro', 0),
(418, 'Indian/Kerguelen', 0),
(419, 'Indian/Mahe', 0),
(420, 'Indian/Maldives', 0),
(421, 'Indian/Mauritius', 0),
(422, 'Indian/Mayotte', 0),
(423, 'Indian/Reunion', 0),
(424, 'Pacific/Apia', 0),
(425, 'Pacific/Auckland', 0),
(426, 'Pacific/Chatham', 0),
(427, 'Pacific/Chuuk', 0),
(428, 'Pacific/Easter', 0),
(429, 'Pacific/Efate', 0),
(430, 'Pacific/Enderbury', 0),
(431, 'Pacific/Fakaofo', 0),
(432, 'Pacific/Fiji', 0),
(433, 'Pacific/Funafuti', 0),
(434, 'Pacific/Galapagos', 0),
(435, 'Pacific/Gambier', 0),
(436, 'Pacific/Guadalcanal', 0),
(437, 'Pacific/Guam', 0),
(438, 'Pacific/Honolulu', 0),
(439, 'Pacific/Johnston', 0),
(440, 'Pacific/Kiritimati', 0),
(441, 'Pacific/Kosrae', 0),
(442, 'Pacific/Kwajalein', 0),
(443, 'Pacific/Majuro', 0),
(444, 'Pacific/Marquesas', 0),
(445, 'Pacific/Midway', 0),
(446, 'Pacific/Nauru', 0),
(447, 'Pacific/Niue', 0),
(448, 'Pacific/Norfolk', 0),
(449, 'Pacific/Noumea', 0),
(450, 'Pacific/Pago_Pago', 0),
(451, 'Pacific/Palau', 0),
(452, 'Pacific/Pitcairn', 0),
(453, 'Pacific/Pohnpei', 0),
(454, 'Pacific/Ponape', 0),
(455, 'Pacific/Port_Moresby', 0),
(456, 'Pacific/Rarotonga', 0),
(457, 'Pacific/Saipan', 0),
(458, 'Pacific/Samoa', 0),
(459, 'Pacific/Tahiti', 0),
(460, 'Pacific/Tarawa', 0),
(461, 'Pacific/Tongatapu', 0),
(462, 'Pacific/Truk', 0),
(463, 'Pacific/Wake', 0),
(464, 'Pacific/Wallis', 0),
(465, 'Pacific/Yap', 0);

-- --------------------------------------------------------

--
-- Tabellstruktur `users`
--

CREATE TABLE `users` (
  `id` mediumint(9) UNSIGNED NOT NULL,
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
  `pmblocked` tinyint(1) NOT NULL DEFAULT '0',
  `tempbanned` int(12) NOT NULL,
  `canreport` tinyint(4) NOT NULL DEFAULT '1',
  `renamethread` tinyint(4) NOT NULL DEFAULT '1',
  `sex` tinyint(4) NOT NULL DEFAULT '2',
  `power` tinyint(4) NOT NULL DEFAULT '0',
  `tzoff` float NOT NULL DEFAULT '0',
  `dateformat` varchar(15) NOT NULL DEFAULT 'm-d-y',
  `timeformat` varchar(15) NOT NULL DEFAULT 'h:i A',
  `ppp` smallint(3) UNSIGNED NOT NULL DEFAULT '20',
  `tpp` smallint(3) UNSIGNED NOT NULL DEFAULT '20',
  `longpages` int(1) NOT NULL DEFAULT '0',
  `fontsize` smallint(5) UNSIGNED NOT NULL DEFAULT '68',
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
  `minipic` text NOT NULL,
  `etc` int(11) NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '1',
  `nick_color` varchar(6) NOT NULL,
  `enablecolor` int(1) NOT NULL DEFAULT '0',
  `hidden` int(1) NOT NULL DEFAULT '0',
  `blocklayouts` int(11) NOT NULL DEFAULT '0',
  `blocksprites` int(11) NOT NULL DEFAULT '0',
  `hidesmilies` int(11) NOT NULL DEFAULT '0',
  `timezone` varchar(128) NOT NULL DEFAULT 'UTC',
  `hidequickreply` int(1) NOT NULL DEFAULT '0',
  `adinfo` text NOT NULL,
  `redirtype` int(1) NOT NULL DEFAULT '0',
  `emailhide` int(1) NOT NULL DEFAULT '0',
  `showlevelbar` int(11) NOT NULL DEFAULT '0',
  `numbargfx` int(11) NOT NULL DEFAULT '0',
  `posttoolbar` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `users`
--

INSERT INTO `users` (`id`, `name`, `displayname`, `pass`, `posts`, `threads`, `regdate`, `lastpost`, `lastview`, `lastforum`, `ip`, `ipfwd`, `url`, `ipbanned`, `pmblocked`, `tempbanned`, `canreport`, `renamethread`, `sex`, `power`, `tzoff`, `dateformat`, `timeformat`, `ppp`, `tpp`, `longpages`, `fontsize`, `theme`, `birth`, `rankset`, `title`, `realname`, `location`, `email`, `homeurl`, `homename`, `usepic`, `head`, `sign`, `signsep`, `bio`, `minipic`, `etc`, `group_id`, `nick_color`, `enablecolor`, `hidden`, `blocklayouts`, `blocksprites`, `hidesmilies`, `timezone`, `hidequickreply`, `adinfo`, `redirtype`, `emailhide`, `showlevelbar`, `numbargfx`, `posttoolbar`) VALUES
(1, 'admin', '', '66170fc2be0bd636035a8f674dcf3886', 0, 0, 1507994275, 0, 1507998199, 0, '127.0.0.1', '', '/Acmlmboard/login.php', 0, 0, 0, 1, 1, 2, 0, 0, 'm-d-y', 'h:i A', 20, 20, 0, 70, 'abxd', '-1', 1, '', '', '', '', '', '', 0, '', '', 0, '', '', 0, 6, '', 0, 0, 0, 0, 0, 'UTC', 0, '', 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellstruktur `usersrpg`
--

CREATE TABLE `usersrpg` (
  `id` mediumint(9) UNSIGNED NOT NULL DEFAULT '0',
  `spent` int(11) NOT NULL DEFAULT '0',
  `gcoins` int(11) NOT NULL DEFAULT '0',
  `eq1` int(5) UNSIGNED NOT NULL DEFAULT '0',
  `eq2` int(5) UNSIGNED NOT NULL DEFAULT '0',
  `eq3` int(5) UNSIGNED NOT NULL DEFAULT '0',
  `eq4` int(5) UNSIGNED NOT NULL DEFAULT '0',
  `eq5` int(5) UNSIGNED NOT NULL DEFAULT '0',
  `eq6` int(5) UNSIGNED NOT NULL DEFAULT '0',
  `lastact` int(11) NOT NULL DEFAULT '0',
  `room` smallint(6) NOT NULL DEFAULT '0',
  `side` tinyint(4) NOT NULL DEFAULT '0',
  `ready` tinyint(4) NOT NULL DEFAULT '0',
  `hp` mediumint(8) NOT NULL DEFAULT '0',
  `mp` mediumint(8) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `usersrpg`
--

INSERT INTO `usersrpg` (`id`, `spent`, `gcoins`, `eq1`, `eq2`, `eq3`, `eq4`, `eq5`, `eq6`, `lastact`, `room`, `side`, `ready`, `hp`, `mp`) VALUES
(1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellstruktur `user_badges`
--

CREATE TABLE `user_badges` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `badge_var` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellstruktur `user_group`
--

CREATE TABLE `user_group` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `sortorder` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumpning av Data i tabell `user_group`
--

INSERT INTO `user_group` (`user_id`, `group_id`, `sortorder`) VALUES
(0, 1, 0),
(-1, 15, 0);

-- --------------------------------------------------------

--
-- Tabellstruktur `user_profileext`
--

CREATE TABLE `user_profileext` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `field_id` varchar(64) NOT NULL,
  `data` varchar(128) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellstruktur `views`
--

CREATE TABLE `views` (
  `view` int(11) NOT NULL,
  `user` mediumint(9) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `views`
--

INSERT INTO `views` (`view`, `user`, `time`) VALUES
(1, 0, 1507992264),
(2, 0, 1507992265),
(3, 0, 1507993126),
(4, 0, 1507993258),
(5, 0, 1507993362),
(6, 0, 1507993365),
(7, 0, 1507993366),
(8, 0, 1507993381),
(9, 0, 1507993848),
(10, 0, 1507994025),
(11, 0, 1507994168),
(12, 0, 1507994170),
(13, 0, 1507994174),
(14, 0, 1507994195),
(15, 0, 1507994239),
(16, 0, 1507994275),
(17, 0, 1507994275),
(18, 0, 1507994278),
(19, 0, 1507994284),
(20, 0, 1507994286),
(21, 0, 1507994310),
(22, 1, 1507994311),
(23, 1, 1507994319),
(24, 1, 1507995185),
(25, 1, 1507996511),
(26, 1, 1507996549),
(27, 1, 1507998183),
(28, 1, 1507998186),
(29, 1, 1507998188),
(30, 1, 1507998191),
(31, 1, 1507998192),
(32, 1, 1507998193),
(33, 1, 1507998194),
(34, 1, 1507998199),
(35, 0, 1507998199);

-- --------------------------------------------------------

--
-- Tabellstruktur `x_perm`
--

CREATE TABLE `x_perm` (
  `id` int(11) NOT NULL,
  `x_id` int(11) NOT NULL,
  `x_type` varchar(64) NOT NULL,
  `perm_id` varchar(64) NOT NULL,
  `permbind_id` varchar(64) NOT NULL,
  `bindvalue` int(11) NOT NULL,
  `revoke` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumpning av Data i tabell `x_perm`
--

INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES
(0, 4, 'group', 'edit-titles', '', 0, 0),
(1, 2, 'group', 'capture-sprites', '', 0, 0),
(2, 2, 'group', 'login', '', 0, 0),
(3, 2, 'group', 'update-own-profile', '', 0, 0),
(4, 1, 'group', 'view-profile-page', '', 0, 0),
(5, 1, 'group', 'view-public-categories', '', 0, 0),
(6, 1, 'group', 'view-public-forums', '', 0, 0),
(7, 1, 'group', 'view-public-posts', '', 0, 0),
(8, 1, 'group', 'view-public-threads', '', 0, 0),
(9, 2, 'group', 'create-public-post', '', 0, 0),
(10, 2, 'group', 'create-public-thread', '', 0, 0),
(11, 2, 'group', 'update-own-post', '', 0, 0),
(12, 2, 'group', 'use-post-layout', '', 0, 0),
(23, 8, 'group', 'consecutive-posts', '', 0, 0),
(24, 3, 'group', 'delete-post', '', 0, 0),
(25, 3, 'group', 'delete-thread', '', 0, 0),
(26, 3, 'group', 'update-post', '', 0, 0),
(27, 3, 'group', 'update-thread', '', 0, 0),
(28, 3, 'group', 'view-post-history', '', 0, 0),
(30, 4, 'group', 'edit-attentions-box', '', 0, 0),
(31, 4, 'group', 'edit-categories', '', 0, 0),
(32, 4, 'group', 'edit-forums', '', 0, 0),
(33, 4, 'group', 'edit-moods', '', 0, 0),
(34, 4, 'group', 'edit-permissions', '', 0, 0),
(36, 4, 'group', 'view-all-private-categories', '', 0, 0),
(37, 4, 'group', 'view-all-private-forums', '', 0, 0),
(38, 4, 'group', 'view-all-private-posts', '', 0, 0),
(39, 4, 'group', 'view-all-private-threads', '', 0, 0),
(40, 4, 'group', 'view-all-sprites', '', 0, 0),
(41, 4, 'group', 'view-permissions', '', 0, 0),
(45, 6, 'group', 'no-restrictions', '', 0, 0),
(46, 7, 'group', 'view-private-forum', 'forum', 11, 0),
(47, 7, 'group', 'edit-forum-thread', 'forum', 11, 0),
(48, 7, 'group', 'delete-forum-thread', 'forum', 11, 0),
(49, 7, 'group', 'edit-forum-post', 'forum', 11, 0),
(50, 7, 'group', 'delete-forum-post', 'forum', 11, 0),
(54, 7, 'group', 'view-private-forum', 'forum', 12, 0),
(55, 7, 'group', 'edit-forum-thread', 'forum', 12, 0),
(56, 7, 'group', 'delete-forum-thread', 'forum', 12, 0),
(57, 7, 'group', 'edit-forum-post', 'forum', 12, 0),
(58, 7, 'group', 'delete-forum-post', 'forum', 12, 0),
(59, 7, 'group', 'edit-forum-thread', 'forum', 13, 0),
(60, 7, 'group', 'delete-forum-thread', 'forum', 13, 0),
(61, 7, 'group', 'edit-forum-post', 'forum', 13, 0),
(62, 7, 'group', 'delete-forum-post', 'forum', 13, 0),
(63, 7, 'group', 'view-private-forum', 'forum', 13, 0),
(64, 4, 'group', 'create-all-private-forum-threads', '', 0, 0),
(65, 4, 'group', 'create-all-private-forum-posts', '', 0, 0),
(66, 10, 'group', 'view-private-category', 'categories', 2, 0),
(67, 10, 'group', 'view-private-forum', 'forum', 2, 0),
(68, 10, 'group', 'view-private-forum', 'forum', 3, 0),
(69, 10, 'group', 'create-private-forum-thread', 'forum', 2, 0),
(70, 10, 'group', 'create-private-forum-post', 'forum', 2, 0),
(71, 10, 'group', 'create-private-forum-thread', 'forum', 3, 0),
(72, 10, 'group', 'create-private-forum-post', 'forum', 3, 0),
(73, 9, 'group', 'create-public-thread', '', 0, 1),
(74, 9, 'group', 'create-public-post', '', 0, 1),
(75, 9, 'group', 'update-own-post', '', 0, 1),
(76, 9, 'group', 'update-own-profile', '', 0, 1),
(77, 4, 'group', 'update-profiles', '', 0, 0),
(78, 9, 'group', 'rate-thread', '', 0, 1),
(79, 2, 'group', 'rate-thread', '', 0, 0),
(80, 1, 'group', 'register', '', 0, 0),
(81, 2, 'group', 'register', '', 0, 1),
(82, 2, 'group', 'logout', '', 0, 0),
(83, 1, 'group', 'view-login', '', 0, 0),
(84, 2, 'group', 'view-login', '', 0, 1),
(85, 2, 'group', 'mark-read', '', 0, 0),
(86, 8, 'group', 'staff', '', 0, 0),
(87, 9, 'group', 'banned', '', 0, 0),
(88, 8, 'group', 'ignore-thread-time-limit', '', 0, 0),
(89, 2, 'group', 'rename-own-thread', '', 0, 0),
(90, 7, 'group', 'view-forum-post-history', 'forum', 11, 0),
(91, 7, 'group', 'view-forum-post-history', 'forum', 12, 0),
(92, 7, 'group', 'view-forum-post-history', 'forum', 13, 0),
(93, 7, 'group', 'create-private-forum-thread', 'forum', 11, 0),
(94, 7, 'group', 'create-private-forum-thread', 'forum', 12, 0),
(95, 7, 'group', 'create-private-forum-thread', 'forum', 13, 0),
(96, 7, 'group', 'create-private-forum-post', 'forum', 11, 0),
(97, 7, 'group', 'create-private-forum-post', 'forum', 12, 0),
(98, 7, 'group', 'create-private-forum-post', 'forum', 13, 0),
(99, 4, 'group', 'view-post-ips', '', 0, 0),
(100, 4, 'group', 'edit-sprites', '', 0, 0),
(101, 2, 'group', 'update-own-moods', '', 0, 0),
(102, 2, 'group', 'view-user-urls', '', 0, 0),
(103, 4, 'group', 'view-hidden-users', '', 0, 0),
(104, 4, 'group', 'edit-users', '', 0, 0),
(105, 3, 'user', 'use-test-bed', '', 0, 0),
(126, 2, 'group', 'create-pms', '', 0, 0),
(127, 2, 'group', 'delete-own-pms', '', 0, 0),
(128, 2, 'group', 'view-own-pms', '', 0, 0),
(129, 8, 'group', 'edit-own-title', '', 0, 0),
(130, 11, 'group', 'create-pms', '', 0, 1),
(131, 11, 'group', 'delete-own-pms', '', 0, 1),
(132, 11, 'group', 'view-own-pms', '', 0, 1),
(133, 2, 'group', 'view-own-sprites', '', 0, 0),
(134, 2, 'group', 'create-public-post', 'forum', 16, 1),
(135, 2, 'group', 'create-public-thread', 'forum', 16, 1),
(136, 12, 'group', 'view-private-forum', 'forum', 15, 0),
(137, 12, 'group', 'create-private-forum-post', 'forum', 15, 0),
(138, 12, 'group', 'create-private-forum-thread', 'forum', 15, 0),
(139, 4, 'group', 'override-readonly-forums', '', 0, 0),
(160, 13, 'group', 'edit-forum-thread', 'forum', 1, 0),
(161, 13, 'group', 'delete-forum-thread', '', 1, 0),
(162, 13, 'group', 'edit-forum-post', '', 1, 0),
(163, 13, 'group', 'delete-forum-post', '', 1, 0),
(164, 13, 'group', 'view-forum-post-history', '', 1, 0),
(170, 10, 'group', 'edit-forum-thread', '', 2, 0),
(171, 10, 'group', 'delete-forum-thread', '', 2, 0),
(172, 10, 'group', 'edit-forum-post', '', 2, 0),
(173, 10, 'group', 'delete-forum-post', '', 2, 0),
(174, 10, 'group', 'view-forum-post-history', '', 2, 0),
(175, 10, 'group', 'edit-forum-thread', '', 3, 0),
(176, 10, 'group', 'delete-forum-thread', '', 3, 0),
(177, 10, 'group', 'edit-forum-post', '', 3, 0),
(178, 10, 'group', 'delete-forum-post', '', 3, 0),
(179, 10, 'group', 'view-forum-post-history', '', 3, 0),
(180, 14, 'group', 'edit-forum-thread', '', 2, 0),
(181, 14, 'group', 'delete-forum-thread', '', 2, 0),
(182, 14, 'group', 'edit-forum-post', '', 2, 0),
(183, 14, 'group', 'delete-forum-post', '', 2, 0),
(184, 14, 'group', 'view-forum-post-history', '', 2, 0),
(185, 9, 'group', 'rename-own-thread', '', 0, 1),
(186, 4, 'group', 'view-errors', '', 0, 0),
(187, 4, 'group', 'edit-ip-bans', '', 0, 0),
(188, 1, 'group', 'view-calendar', '', 0, 0),
(189, 15, 'group', 'view-calendar', '', 0, 1),
(190, 10, 'group', 'view-private-forum', '', 17, 0),
(191, 10, 'group', 'create-private-forum-post', '', 17, 0),
(192, 10, 'group', 'create-private-forum-thread', '', 17, 0),
(193, 10, 'group', 'edit-forum-thread', '', 17, 0),
(194, 10, 'group', 'delete-forum-thread', '', 17, 0),
(195, 10, 'group', 'edit-forum-post', '', 17, 0),
(196, 10, 'group', 'delete-forum-post', '', 17, 0),
(197, 10, 'group', 'view-forum-post-history', '', 17, 0),
(198, 3, 'group', 'create-all-forums-announcement', '', 0, 0),
(199, 10, 'group', 'create-forum-announcement', '', 2, 0),
(200, 10, 'group', 'create-forum-announcement', '', 3, 0),
(201, 16, 'group', 'view-private-forum', '', 21, 0),
(202, 16, 'group', 'create-private-forum-post', '', 21, 0),
(203, 16, 'group', 'create-private-forum-thread', '', 21, 0),
(204, 16, 'group', 'view-private-category', '', 9, 0),
(205, 5, 'group', 'view-private-forum', '', 21, 0),
(206, 5, 'group', 'create-private-forum-post', '', 21, 0),
(207, 5, 'group', 'create-private-forum-thread', '', 21, 0),
(208, 16, 'group', 'edit-forum-thread', '', 21, 0),
(209, 16, 'group', 'delete-forum-thread', '', 21, 0),
(210, 16, 'group', 'edit-forum-post', '', 21, 0),
(211, 16, 'group', 'delete-forum-post', '', 21, 0),
(212, 16, 'group', 'view-forum-post-history', '', 21, 0),
(213, 7, 'user', 'edit-sprites', '', 0, 0),
(214, 10, 'group', 'has-displayname', '', 0, 0),
(215, 10, 'group', 'view-acs-calendar', '', 0, 0),
(216, 49, 'user', 'view-acs-calendar', '', 0, 0),
(217, 2, 'group', 'post-radar', '', 0, 0),
(218, 3, 'group', 'show-as-staff', '', 0, 0),
(219, 4, 'group', 'show-as-staff', '', 0, 0),
(220, 8, 'group', 'show-as-staff', '', 0, 0),
(221, 6, 'group', 'show-as-staff', '', 0, 0),
(222, 10, 'group', 'track-ip-change', '', 0, 0),
(223, 2, 'group', 'use-item-shop', '', 0, 0),
(224, 2, 'group', 'block-layout', '', 0, 0),
(226, 12, 'user', 'edit-forum-post', '', 18, 0),
(227, 12, 'user', 'edit-forum-thread', '', 18, 0),
(228, 12, 'user', 'delete-forum-post', '', 18, 0),
(229, 12, 'user', 'delete-forum-thread', '', 18, 0),
(230, 12, 'user', 'view-forum-post-history', '', 18, 0),
(232, 9, 'group', 'edit-own-title', '', 0, 1),
(234, 7, 'user', 'view-user-pms', '', 0, 0),
(235, 116, 'user', 'create-pms', '', 0, 1),
(236, 16, 'group', 'edit-forum-thread', '', 23, 0),
(237, 16, 'group', 'delete-forum-thread', '', 23, 0),
(238, 16, 'group', 'edit-forum-post', '', 23, 0),
(239, 16, 'group', 'delete-forum-post', '', 23, 0),
(240, 16, 'group', 'view-forum-post-history', '', 23, 0),
(241, 102, 'user', 'view-acs-calendar', '', 0, 0),
(243, 17, 'group', 'view-post-history', '', 22, 0),
(244, 16, 'group', 'edit-forum-thread', 'forum', 6, 0),
(245, 102, 'user', 'consecutive-posts', '', 0, 0),
(246, 7, 'user', 'has-displayname', '', 0, 0),
(247, 13, 'user', 'edit-forum-post', '', 18, 0),
(248, 13, 'user', 'delete-post', '', 18, 0),
(249, 13, 'user', 'view-post-history', '', 18, 0),
(250, 13, 'user', 'edit-forum-thread', '', 18, 0),
(251, 13, 'user', 'delete-forum-thread', '', 18, 0),
(252, 13, 'user', 'consecutive-posts', '', 0, 0),
(253, 10, 'group', 'view-allranks', '', 0, 0),
(254, 3, 'group', 'override-closed-all', '', 0, 0),
(255, 4, 'group', 'edit-user-show-online', '', 0, 0),
(256, 4, 'group', 'edit-user-show-online', '', 0, 0),
(257, 4, 'group', 'edit-user-show-online', '', 0, 0),
(258, 4, 'group', 'update-extended-profiles', '', 0, 0),
(259, 4, 'group', 'update-extended-profiles', '', 0, 0),
(260, 4, 'group', 'update-extended-profiles', '', 0, 0),
(261, 4, 'group', 'update-extended-profiles', '', 0, 0),
(262, 4, 'group', 'update-extended-profiles', '', 0, 0),
(263, 4, 'group', 'update-extended-profiles', '', 0, 0),
(264, 4, 'group', 'update-extended-profiles', '', 0, 0),
(265, 4, 'group', 'update-extended-profiles', '', 0, 0),
(266, 2, 'group', 'show-online', '', 0, 0),
(267, 4, 'group', 'manage-board', '', 0, 0),
(268, 4, 'group', 'manage-board', '', 0, 0),
(269, 4, 'group', 'manage-board', '', 0, 0),
(270, 4, 'group', 'manage-board', '', 0, 0),
(271, 4, 'group', 'manage-board', '', 0, 0),
(272, 4, 'group', 'manage-board', '', 0, 0),
(273, 4, 'group', 'manage-board', '', 0, 0),
(274, 4, 'group', 'edit-all-group', '', 0, 0),
(275, 4, 'group', 'edit-all-group', '', 0, 0),
(276, 4, 'group', 'edit-all-group', '', 0, 0),
(277, 4, 'group', 'edit-all-group', '', 0, 0),
(278, 4, 'group', 'edit-all-group', '', 0, 0),
(279, 4, 'group', 'edit-all-group', '', 0, 0),
(280, 4, 'group', 'edit-all-group', '', 0, 0),
(281, 4, 'group', 'edit-all-group', '', 0, 0),
(282, 4, 'group', 'edit-all-group', '', 0, 0),
(283, 4, 'group', 'edit-all-group', '', 0, 0),
(284, 4, 'group', 'edit-all-group', '', 0, 0),
(285, 4, 'group', 'edit-all-group', '', 0, 0),
(286, 4, 'group', 'edit-all-group', '', 0, 0),
(287, 1, 'group', 'create-public-thread', '', 0, 0),
(288, 1, 'group', 'create-public-thread', '', 0, 0),
(289, 1, 'group', 'create-public-thread', '', 0, 0),
(290, 1, 'group', 'create-public-thread', '', 0, 0),
(291, 1, 'group', 'create-public-thread', '', 0, 0),
(292, 1, 'group', 'create-public-thread', '', 0, 0),
(293, 1, 'group', 'create-public-thread', '', 0, 0),
(294, 3, 'group', 'ban-users', '', 0, 0),
(295, 3, 'group', 'ban-users', '', 0, 0),
(296, 3, 'group', 'ban-users', '', 0, 0),
(297, 3, 'group', 'ban-users', '', 0, 0),
(298, 3, 'group', 'ban-users', '', 0, 0),
(299, 3, 'group', 'ban-users', '', 0, 0),
(300, 3, 'group', 'ban-users', '', 0, 0),
(301, 3, 'group', 'ban-users', '', 0, 0),
(302, 3, 'group', 'ban-users', '', 0, 0),
(303, 3, 'group', 'ban-users', '', 0, 0),
(304, 4, 'group', 'edit-titles', '', 0, 0),
(305, 4, 'group', 'edit-titles', '', 0, 0),
(306, 4, 'group', 'edit-titles', '', 0, 0),
(307, 4, 'group', 'edit-titles', '', 0, 0),
(308, 4, 'group', 'edit-titles', '', 0, 0),
(309, 4, 'group', 'edit-titles', '', 0, 0),
(310, 4, 'group', 'edit-titles', '', 0, 0),
(311, 4, 'group', 'edit-titles', '', 0, 0),
(312, 4, 'group', 'edit-titles', '', 0, 0),
(313, 4, 'group', 'edit-titles', '', 0, 0),
(314, 4, 'group', 'edit-titles', '', 0, 0),
(315, 4, 'group', 'edit-titles', '', 0, 0),
(316, 2, 'group', 'deleted-posts-tracker', '', 0, 0),
(317, 2, 'group', 'deleted-posts-tracker', '', 0, 0),
(318, 2, 'group', 'view-favorites', '', 0, 0),
(319, 4, 'group', 'edit-ranks', '', 0, 0),
(320, 4, 'group', 'has-customusercolor', '', 0, 0);

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `annoucenickprefix`
--
ALTER TABLE `annoucenickprefix`
  ADD PRIMARY KEY (`group_id`);

--
-- Index för tabell `announcechans`
--
ALTER TABLE `announcechans`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `badgecateg`
--
ALTER TABLE `badgecateg`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `priority` (`priority`);

--
-- Index för tabell `blockedlayouts`
--
ALTER TABLE `blockedlayouts`
  ADD KEY `user` (`user`);

--
-- Index för tabell `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `dailystats`
--
ALTER TABLE `dailystats`
  ADD PRIMARY KEY (`date`);

--
-- Index för tabell `deletedgroups`
--
ALTER TABLE `deletedgroups`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `forummods`
--
ALTER TABLE `forummods`
  ADD UNIQUE KEY `uid_2` (`uid`,`fid`),
  ADD KEY `uid` (`uid`,`fid`);

--
-- Index för tabell `forums`
--
ALTER TABLE `forums`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `forumsread`
--
ALTER TABLE `forumsread`
  ADD UNIQUE KEY `uid` (`uid`,`fid`);

--
-- Index för tabell `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `guests`
--
ALTER TABLE `guests`
  ADD UNIQUE KEY `ip` (`ip`);

--
-- Index för tabell `hourlyviews`
--
ALTER TABLE `hourlyviews`
  ADD UNIQUE KEY `hour` (`hour`);

--
-- Index för tabell `itemcateg`
--
ALTER TABLE `itemcateg`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cat` (`cat`);

--
-- Index för tabell `log`
--
ALTER TABLE `log`
  ADD KEY `t` (`t`),
  ADD KEY `ip` (`ip`);

--
-- Index för tabell `mcache`
--
ALTER TABLE `mcache`
  ADD KEY `hash` (`hash`);

--
-- Index för tabell `misc`
--
ALTER TABLE `misc`
  ADD PRIMARY KEY (`field`);

--
-- Index för tabell `mood`
--
ALTER TABLE `mood`
  ADD UNIQUE KEY `id` (`id`,`user`);

--
-- Index för tabell `perm`
--
ALTER TABLE `perm`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `permbind`
--
ALTER TABLE `permbind`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `permcat`
--
ALTER TABLE `permcat`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `pmsgs`
--
ALTER TABLE `pmsgs`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `pmsgstext`
--
ALTER TABLE `pmsgstext`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `polloptions`
--
ALTER TABLE `polloptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poll` (`poll`);

--
-- Index för tabell `polls`
--
ALTER TABLE `polls`
  ADD KEY `id` (`id`);

--
-- Index för tabell `pollvotes`
--
ALTER TABLE `pollvotes`
  ADD UNIQUE KEY `id_2` (`id`,`user`);

--
-- Index för tabell `posticons`
--
ALTER TABLE `posticons`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `threadid` (`thread`);

--
-- Index för tabell `poststext`
--
ALTER TABLE `poststext`
  ADD PRIMARY KEY (`id`,`revision`);

--
-- Index för tabell `post_radar`
--
ALTER TABLE `post_radar`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `user_id` (`user_id`,`user2_id`);

--
-- Index för tabell `profileext`
--
ALTER TABLE `profileext`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `ranksets`
--
ALTER TABLE `ranksets`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `resetpass`
--
ALTER TABLE `resetpass`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Index för tabell `rpgchat`
--
ALTER TABLE `rpgchat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chan` (`chan`);

--
-- Index för tabell `rpgrooms`
--
ALTER TABLE `rpgrooms`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `sprites`
--
ALTER TABLE `sprites`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `sprite_captures`
--
ALTER TABLE `sprite_captures`
  ADD UNIQUE KEY `userid` (`userid`,`monid`);

--
-- Index för tabell `threads`
--
ALTER TABLE `threads`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `threadsread`
--
ALTER TABLE `threadsread`
  ADD UNIQUE KEY `uid` (`uid`,`tid`);

--
-- Index för tabell `threadthumbs`
--
ALTER TABLE `threadthumbs`
  ADD UNIQUE KEY `uid` (`uid`,`tid`);

--
-- Index för tabell `timezones`
--
ALTER TABLE `timezones`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index för tabell `usersrpg`
--
ALTER TABLE `usersrpg`
  ADD UNIQUE KEY `id` (`id`);

--
-- Index för tabell `user_badges`
--
ALTER TABLE `user_badges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `badge_id` (`badge_id`);

--
-- Index för tabell `user_profileext`
--
ALTER TABLE `user_profileext`
  ADD PRIMARY KEY (`id`);

--
-- Index för tabell `views`
--
ALTER TABLE `views`
  ADD UNIQUE KEY `view` (`view`);

--
-- Index för tabell `x_perm`
--
ALTER TABLE `x_perm`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `announcechans`
--
ALTER TABLE `announcechans`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT för tabell `badgecateg`
--
ALTER TABLE `badgecateg`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT för tabell `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT för tabell `deletedgroups`
--
ALTER TABLE `deletedgroups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT för tabell `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT för tabell `group`
--
ALTER TABLE `group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT för tabell `itemcateg`
--
ALTER TABLE `itemcateg`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT för tabell `items`
--
ALTER TABLE `items`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT för tabell `permcat`
--
ALTER TABLE `permcat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT för tabell `pmsgs`
--
ALTER TABLE `pmsgs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT för tabell `polloptions`
--
ALTER TABLE `polloptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT för tabell `posticons`
--
ALTER TABLE `posticons`
  MODIFY `id` tinyint(4) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT för tabell `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT för tabell `post_radar`
--
ALTER TABLE `post_radar`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT för tabell `resetpass`
--
ALTER TABLE `resetpass`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT för tabell `rpgchat`
--
ALTER TABLE `rpgchat`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT för tabell `rpgrooms`
--
ALTER TABLE `rpgrooms`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT för tabell `sprites`
--
ALTER TABLE `sprites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT för tabell `threads`
--
ALTER TABLE `threads`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT för tabell `timezones`
--
ALTER TABLE `timezones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=466;
--
-- AUTO_INCREMENT för tabell `users`
--
ALTER TABLE `users`
  MODIFY `id` mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT för tabell `user_badges`
--
ALTER TABLE `user_badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT för tabell `user_profileext`
--
ALTER TABLE `user_profileext`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT för tabell `x_perm`
--
ALTER TABLE `x_perm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=321;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
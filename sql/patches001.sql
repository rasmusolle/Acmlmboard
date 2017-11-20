-- Random shit episode 001

-- sPrItE rEmOvAl
DROP TABLE spritecateg;
DROP TABLE sprites;
DROP TABLE sprite_captures;

DELETE FROM `perm` WHERE `perm`.`id` = 'capture-sprites';
DELETE FROM `perm` WHERE `perm`.`id` = 'edit-sprites';
DELETE FROM `perm` WHERE `perm`.`id` = 'view-all-sprites';
DELETE FROM `perm` WHERE `perm`.`id` = 'view-own-sprites';

DELETE FROM `x_perm` WHERE `x_perm`.`id` = 1;
DELETE FROM `x_perm` WHERE `x_perm`.`id` = 40;
DELETE FROM `x_perm` WHERE `x_perm`.`id` = 100;
DELETE FROM `x_perm` WHERE `x_perm`.`id` = 133;
DELETE FROM `x_perm` WHERE `x_perm`.`id` = 213;


-- Post raderr rremoval
DROP TABLE post_radar;


-- Kirby and Zelda rankset removals.
DELETE FROM `ranksets` WHERE `ranksets`.`id` = 2;
DELETE FROM `ranks` WHERE `ranks`.`rs` = 2;
DELETE FROM `ranksets` WHERE `ranksets`.`id` = 3;
DELETE FROM `ranks` WHERE `ranks`.`rs` = 3;


-- Remove IRC reporting.
DROP TABLE annoucenickprefix;
DROP TABLE announcechans;
ALTER TABLE `forums` DROP `announcechan_id`;
ALTER TABLE `group` DROP `char`;
ALTER TABLE `group` DROP `irc_color`;


-- Deleted Posts Tracker
DELETE FROM `perm` WHERE `perm`.`id` = 'deleted-posts-tracker';
DELETE FROM `perm` WHERE `perm`.`id` = 'track-deleted-posts';
DELETE FROM `x_perm` WHERE `x_perm`.`id` = 316;
DELETE FROM `x_perm` WHERE `x_perm`.`id` = 317;
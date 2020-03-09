CREATE TABLE `simple_q` (
	`created` datetime NOT NULL DEFAULT current_timestamp(),
	`updated` datetime DEFAULT NULL,
	`queue` char(32) CHARACTER SET latin1 NOT NULL DEFAULT '',
	`status` tinyint(3) unsigned NOT NULL DEFAULT 0,
	`token` char(40) CHARACTER SET latin1 DEFAULT NULL,
	`payload` longblob NOT NULL,
	KEY `idx_token` (`token`) USING BTREE,
	KEY `idx_status` (`status`) USING BTREE,
	KEY `idx_updated` (`updated`) USING BTREE,
	KEY `idx_queue` (`queue`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8
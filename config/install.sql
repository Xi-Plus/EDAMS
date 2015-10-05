CREATE TABLE `account` (
  `id` int(11) NOT NULL,
  `user` char(32) NOT NULL,
  `pwd` char(64) NOT NULL,
  `name` char(32) NOT NULL,
  `power` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `del_test` (
  `muzzle` double DEFAULT NULL,
  `terminal` double NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `aval` int(11) NOT NULL DEFAULT '1',
  `token` char(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `log` (
  `operate` int(11) DEFAULT NULL,
  `affect` int(11) DEFAULT NULL,
  `type` char(255) DEFAULT NULL,
  `result` char(255) DEFAULT NULL,
  `action` text,
  `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `randcode` char(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `cookie` char(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tablelist` (
  `name` char(255) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `aval` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

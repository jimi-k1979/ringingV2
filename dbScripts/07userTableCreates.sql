CREATE TABLE IF NOT EXISTS `users`
(
    `id`           INT(10) UNSIGNED                                            NOT NULL AUTO_INCREMENT,
    `email`        VARCHAR(249) COLLATE utf8mb4_unicode_ci                     NOT NULL,
    `password`     VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    `username`     VARCHAR(100) COLLATE utf8mb4_unicode_ci                              DEFAULT NULL,
    `status`       TINYINT(2) UNSIGNED                                         NOT NULL DEFAULT '0',
    `verified`     TINYINT(1) UNSIGNED                                         NOT NULL DEFAULT '0',
    `resettable`   TINYINT(1) UNSIGNED                                         NOT NULL DEFAULT '1',
    `roles_mask`   INT(10) UNSIGNED                                            NOT NULL DEFAULT '0',
    `registered`   INT(10) UNSIGNED                                            NOT NULL,
    `last_login`   INT(10) UNSIGNED                                                     DEFAULT NULL,
    `force_logout` MEDIUMINT(7) UNSIGNED                                       NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_confirmations`
(
    `id`       INT(10) UNSIGNED                                            NOT NULL AUTO_INCREMENT,
    `user_id`  INT(10) UNSIGNED                                            NOT NULL,
    `email`    VARCHAR(249) COLLATE utf8mb4_unicode_ci                     NOT NULL,
    `selector` VARCHAR(16) CHARACTER SET latin1 COLLATE latin1_general_cs  NOT NULL,
    `token`    VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    `expires`  INT(10) UNSIGNED                                            NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `selector` (`selector`),
    KEY `email_expires` (`email`, `expires`),
    KEY `user_id` (`user_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_remembered`
(
    `id`       BIGINT(20) UNSIGNED                                         NOT NULL AUTO_INCREMENT,
    `user`     INT(10) UNSIGNED                                            NOT NULL,
    `selector` VARCHAR(24) CHARACTER SET latin1 COLLATE latin1_general_cs  NOT NULL,
    `token`    VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    `expires`  INT(10) UNSIGNED                                            NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `selector` (`selector`),
    KEY `user` (`user`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_resets`
(
    `id`       BIGINT(20) UNSIGNED                                         NOT NULL AUTO_INCREMENT,
    `user`     INT(10) UNSIGNED                                            NOT NULL,
    `selector` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_general_cs  NOT NULL,
    `token`    VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    `expires`  INT(10) UNSIGNED                                            NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `selector` (`selector`),
    KEY `user_expires` (`user`, `expires`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_throttling`
(
    `bucket`         VARCHAR(44) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    `tokens`         FLOAT UNSIGNED                                             NOT NULL,
    `replenished_at` INT(10) UNSIGNED                                           NOT NULL,
    `expires_at`     INT(10) UNSIGNED                                           NOT NULL,
    PRIMARY KEY (`bucket`),
    KEY `expires_at` (`expires_at`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

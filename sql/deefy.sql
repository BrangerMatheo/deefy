-- ============================================================
--  Deefy - script de création de la base de données
--  Tables : user, playlist, track, playlist2track, user2playlist
-- ============================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `user2playlist`;
DROP TABLE IF EXISTS `playlist2track`;
DROP TABLE IF EXISTS `track`;
DROP TABLE IF EXISTS `playlist`;
DROP TABLE IF EXISTS `user`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `user` (
  `id`     INT          NOT NULL AUTO_INCREMENT,
  `email`  VARCHAR(256) NOT NULL,
  `passwd` VARCHAR(256) NOT NULL,
  `role`   INT          NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `playlist` (
  `id`  INT          NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- type : 'A' = piste d'album, 'P' = piste de podcast
-- genre : entier correspondant aux constantes de AudioTrack (0 = Blues ... 21 = Trap)
-- filename : nom du fichier audio stocké dans le dossier audio/
CREATE TABLE `track` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `titre`         VARCHAR(256) NOT NULL,
  `genre`         INT              NULL,
  `duree`         INT              NULL,
  `filename`      VARCHAR(256)     NULL,
  `type`          CHAR(1)      NOT NULL DEFAULT 'A',
  `artiste_album` VARCHAR(256)     NULL,
  `titre_album`   VARCHAR(256)     NULL,
  `annee_album`   INT              NULL,
  `numero_album`  INT              NULL,
  `auteur_podcast` VARCHAR(256)    NULL,
  `date_podcast`  DATE             NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `playlist2track` (
  `id_pl`               INT NOT NULL,
  `id_track`            INT NOT NULL,
  `no_piste_dans_liste` INT NOT NULL,
  PRIMARY KEY (`id_pl`, `id_track`),
  CONSTRAINT `fk_p2t_playlist` FOREIGN KEY (`id_pl`)    REFERENCES `playlist` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_p2t_track`    FOREIGN KEY (`id_track`) REFERENCES `track` (`id`)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user2playlist` (
  `id_user` INT NOT NULL,
  `id_pl`   INT NOT NULL,
  PRIMARY KEY (`id_user`, `id_pl`),
  CONSTRAINT `fk_u2p_user`     FOREIGN KEY (`id_user`) REFERENCES `user` (`id`)     ON DELETE CASCADE,
  CONSTRAINT `fk_u2p_playlist` FOREIGN KEY (`id_pl`)   REFERENCES `playlist` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

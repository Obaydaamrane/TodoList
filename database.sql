CREATE DATABASE IF NOT EXISTS todolist;
USE todolist;
CREATE TABLE IF NOT EXISTS `todo` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(2048) NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

-- Insertion de quelques tâches d'exemple pour tester
INSERT INTO `todo` (`title`, `done`) VALUES 
('Arroser les plantes', 0),
('Terminer l\'activité 7 du module Approche agile', 0),
('Appeler le technicien qui répare mon ancien tél', 0),
('Acheter le riz et du lait', 0);

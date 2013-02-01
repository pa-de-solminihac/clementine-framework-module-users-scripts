<?php
/**
 * Script non interactif de mise à jour du module users depuis la version 2.17 vers la version 2.18
 */

// deja appele par l'installer
// $db->beginTransaction();

// ajout du champ is_alias_of pour la fonctionnalité (encore en cours de dev) d'alias d'utilisateurs

$sql = <<<SQL

ALTER TABLE `clementine_users` ADD `is_alias_of` INT( 10 ) unsigned DEFAULT NULL AFTER `id`;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$sql = <<<SQL

ALTER TABLE `clementine_users` ADD CONSTRAINT `fk_clementine_users_is_alias_of1` FOREIGN KEY (`is_alias_of`) REFERENCES `clementine_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

// deja appele par l'installer
// $db->commit();
return true;
?>

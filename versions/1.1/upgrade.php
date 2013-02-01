<?php
/**
 * Script non interactif de mise à jour du module users depuis la version 1.0 vers la version 1.1
 */

// deja appele par l'installer
// $db->beginTransaction();

$sql = <<<SQL

-- -----------------------------------------------------
-- Table `clementine_users`
-- -----------------------------------------------------
ALTER TABLE `clementine_users` ADD `id_parent` INT NULL AFTER `active`;

-- -----------------------------------------------------
-- Table `clementine_users_groups`
-- -----------------------------------------------------
ALTER TABLE `clementine_users_groups` ADD `id_parent` INT NULL AFTER `group`;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

// deja appele par l'installer
// $db->commit();
return true;
?>

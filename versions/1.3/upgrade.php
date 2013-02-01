<?php
/**
 * Script non interactif de mise à jour du module users depuis la version 1.2 vers la version 1.3
 */

// deja appele par l'installer
// $db->beginTransaction();

$sql = <<<SQL

-- -----------------------------------------------------
-- Table `clementine_users_has_groups`
-- -----------------------------------------------------
ALTER TABLE `clementine_users_has_groups` DROP FOREIGN KEY `fk_clementine_users_groups_clementine_users` ;
ALTER TABLE `clementine_users_has_groups` DROP INDEX `fk_clementine_users_groups_clementine_users` ;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$sql = <<<SQL

ALTER TABLE `clementine_users_has_groups` ADD INDEX `fk_clementine_users_groups_clementine_users` (`user_id` ASC) ;
ALTER TABLE `clementine_users_has_groups` ADD CONSTRAINT `clementine_users_has_groups_fk1` FOREIGN KEY (`user_id`) REFERENCES `clementine_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$sql = <<<SQL

ALTER TABLE `clementine_users_has_groups` DROP FOREIGN KEY `fk_clementine_users_groups_clementine_users_groups1` ;
ALTER TABLE `clementine_users_has_groups` DROP INDEX `fk_clementine_users_groups_clementine_users_groups1` ;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$sql = <<<SQL

ALTER TABLE `clementine_users_has_groups` ADD INDEX `fk_clementine_users_groups_clementine_users_groups1` (`group_id` ASC) ;
ALTER TABLE `clementine_users_has_groups` ADD CONSTRAINT `clementine_users_has_groups_fk2` FOREIGN KEY (`group_id`) REFERENCES `clementine_users_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$sql = <<<SQL

-- -----------------------------------------------------
-- Table `clementine_users_groups_has_privileges`
-- -----------------------------------------------------
ALTER TABLE `clementine_users_groups_has_privileges` DROP FOREIGN KEY `fk_clementine_users_groups_has_privileges_groups1` ;
ALTER TABLE `clementine_users_groups_has_privileges` DROP INDEX `fk_clementine_users_groups_has_privileges_groups1` ;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$sql = <<<SQL

ALTER TABLE `clementine_users_groups_has_privileges` ADD INDEX `clementine_users_groups_has_privileges_fk1` (`group_id` ASC) ;
ALTER TABLE `clementine_users_groups_has_privileges` ADD CONSTRAINT `clementine_users_groups_has_privileges_fk1` FOREIGN KEY (`group_id`) REFERENCES `clementine_users_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$sql = <<<SQL

ALTER TABLE `clementine_users_groups_has_privileges` DROP FOREIGN KEY `fk_clementine_users_groups_has_privileges_privileges1` ;
ALTER TABLE `clementine_users_groups_has_privileges` DROP INDEX `fk_clementine_users_groups_has_privileges_privileges1` ;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$sql = <<<SQL

ALTER TABLE `clementine_users_groups_has_privileges` ADD INDEX `clementine_users_groups_has_privileges_fk2` (`privilege_id` ASC) ;
ALTER TABLE `clementine_users_groups_has_privileges` ADD CONSTRAINT `clementine_users_groups_has_privileges_fk2` FOREIGN KEY (`privilege_id`) REFERENCES `clementine_users_privileges` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

// deja appele par l'installer
// $db->commit();
return true;
?>

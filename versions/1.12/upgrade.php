<?php
/**
 * Script non interactif d'installation du module utilisateurs
 */

// deja appele par l'installer
// $db->beginTransaction();

$sql = <<<SQL
ALTER TABLE `clementine_users_groups` CHANGE `group` `group` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

// deja appele par l'installer
// $db->commit();
return true;
?>

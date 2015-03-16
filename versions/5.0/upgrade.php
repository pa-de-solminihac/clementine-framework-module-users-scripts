<?php
/**
 * Script non interactif de mise à jour :
 */

// deja appele par l'installer
// $db->beginTransaction();

$requetes = array (

    "
        ALTER TABLE `clementine_users_privileges` ADD UNIQUE(`privilege`);
    ",

    "
        CREATE TABLE IF NOT EXISTS `clementine_users_has_privileges` (
            `user_id` INT UNSIGNED NOT NULL,
            `privilege_id` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`user_id`, `privilege_id`),
            CONSTRAINT `fk_clementine_users_has_privileges_user_id` FOREIGN KEY (`user_id`) REFERENCES `clementine_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `fk_clementine_users_has_privileges_privileges_id` FOREIGN KEY (`privilege_id`) REFERENCES `clementine_users_privileges` (`id`) ON DELETE CASCADE ON UPDATE CASCADE)
            ENGINE = InnoDB
            DEFAULT CHARACTER SET = utf8;
    "
);

// execute les requetes une par une et rollback au moindre plantage
foreach ($requetes as $sql) {
    if (!$db->prepare($sql)->execute()) {
        $db->rollBack();
        return false;
    }
}

// deja appele par l'installer
// $db->commit();

return true;
?>

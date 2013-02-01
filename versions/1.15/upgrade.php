<?php
/**
 * Script non interactif de mise à jour du module users depuis la version 1.14 vers la version 1.15
 */

// deja appele par l'installer
// $db->beginTransaction();

// creation des tables

$sql = <<<SQL

    CREATE TABLE clementine_users_treepaths (
        ancestor INT UNSIGNED NOT NULL,
        descendant INT UNSIGNED NOT NULL,
        depth SMALLINT NOT NULL,
        PRIMARY KEY (ancestor, descendant),
        CONSTRAINT `clementine_users_treepaths_fk1` FOREIGN KEY (ancestor) REFERENCES clementine_users(id) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `clementine_users_treepaths_fk2` FOREIGN KEY (descendant) REFERENCES clementine_users(id) ON DELETE CASCADE ON UPDATE CASCADE
    )
    ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$sql = <<<SQL

    CREATE TABLE clementine_users_groups_treepaths (
        ancestor INT UNSIGNED NOT NULL,
        descendant INT UNSIGNED NOT NULL,
        depth SMALLINT NOT NULL,
        PRIMARY KEY (ancestor, descendant),
        CONSTRAINT `clementine_users_groups_treepaths_fk1` FOREIGN KEY (ancestor) REFERENCES clementine_users_groups(id) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `clementine_users_groups_treepaths_fk2` FOREIGN KEY (descendant) REFERENCES clementine_users_groups(id) ON DELETE CASCADE ON UPDATE CASCADE
    )
    ENGINE = InnoDB
    DEFAULT CHARACTER SET = utf8;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

// migration des donnees : id

// profondeur 0
$sql = 'SELECT * FROM clementine_users';
if (($res = $db->query($sql)->fetchAll()) === false) {
    $db->rollBack();
    return false;
}
foreach ($res as $key => $row) {
    $sql_ins = "INSERT INTO `clementine_users_treepaths` (`ancestor`, `descendant`, `depth`) VALUES ('" . $row['id'] . "', '" . $row['id'] . "', '0'); ";
    if (!$db->prepare($sql_ins)->execute()) {
        $db->rollBack();
        return false;
    }
}

$sql = 'SELECT * FROM clementine_users_groups';
if (($res = $db->query($sql)->fetchAll()) === false) {
    $db->rollBack();
    return false;
}
foreach ($res as $key => $row) {
    $sql_ins = "INSERT INTO `clementine_users_groups_treepaths` (`ancestor`, `descendant`, `depth`) VALUES ('" . $row['id'] . "', '" . $row['id'] . "', '0'); ";
    if (!$db->prepare($sql_ins)->execute()) {
        $db->rollBack();
        return false;
    }
}

// migration des donnees : id_parent

// profondeur >= 1
$maxdepth = 5;
$tables = array('clementine_users', 'clementine_users_groups');
foreach ($tables as $table) {
    $sql  = 'SELECT c1.id AS id1';
    for ($i = 1; $i <= $maxdepth; ++$i) {
        $sql .= ', c' . ($i + 1) . '.id AS id' . ($i + 1) . ' ';
    }
    $sql .= 'FROM ' . $table . ' c1 ';
    for ($i = 1; $i <= $maxdepth; ++$i) {
        $sql .= '
        LEFT JOIN ' . $table . ' c' . ($i + 1) . ' ON (c' . ($i + 1) . '.id_parent = c' . $i . '.id) ';
    }
    $sql .= 'WHERE c2.id > 0 AND c1.id != c2.id';
    $old_fetch_mode = $db->getAttribute(PDO::ATTR_DEFAULT_FETCH_MODE);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_NUM);
    if (($res = $db->query($sql)->fetchAll()) === false) {
        $db->rollBack();
        return false;
    }
    foreach ($res as $key => $row) {
        foreach ($row as $depth => $id) {
            $descendant = null;
            if ($depth === 0) {
                $ancestor = $id;
            } else {
                $descendant = $id;
            }
            if ($descendant > 0 && $depth > 0 && $ancestor != $descendant) {
                // INSERT IGNORE pour que les cas de doublons ne plantent pas l'upgrade
                $sql_ins = "INSERT IGNORE INTO `" . $table . "_treepaths` (`ancestor`, `descendant`, `depth`) VALUES ('" . $ancestor . "', '" . $descendant . "', " . $depth . "); ";
                if (!$db->prepare($sql_ins)->execute()) {
                    $db->rollBack();
                    return false;
                }
            }
        }
    }
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, $old_fetch_mode);
}

// suppression des champs obsoletes

$sql = <<<SQL

    ALTER TABLE `clementine_users` DROP `id_parent`;
    ALTER TABLE `clementine_users_groups` DROP `id_parent`;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

// ajout des nouveaux privileges et lien avec le groupe 'administrateurs'

$sql = <<<SQL

    INSERT INTO `clementine_users_privileges` (`id`, `privilege`) VALUES (0, 'list_users');

    INSERT INTO `clementine_users_groups_has_privileges` (`group_id`, `privilege_id`)
        (SELECT `clementine_users_groups`.`id` AS `group_id`, `clementine_users_privileges`.`id` AS `privilege_id`
           FROM `clementine_users_groups` , `clementine_users_privileges`
          WHERE `group` = 'administrateurs'
            AND `privilege` = 'list_users');

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

// deja appele par l'installer
// $db->commit();
return true;
?>

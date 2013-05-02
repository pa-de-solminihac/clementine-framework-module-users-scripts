<?php
/**
 * Script non interactif de mise à jour : mise à jour des utilisateurs de type adjoint pour leur attribuer les mêmes parents que leur utilisateur maitre
 */

// deja appele par l'installer
// $db->beginTransaction();

$requetes = array (

    // supprime tous les enregistrements sur de parents pour les comptes adjoints
    "DELETE
       FROM `clementine_users_treepaths`
      WHERE `depth` > 0 AND `descendant` IN (
         SELECT DISTINCT `id`
           FROM `clementine_users` cu 
          WHERE `is_alias_of` IS NOT NULL
     );",

    // pour chaque adjoint, on enregistre les memes parents que pour son compte maitre
    "INSERT INTO `clementine_users_treepaths` (
         `ancestor`,
         `descendant`,
         `depth`
     ) SELECT cut.ancestor, cu.id, cut.depth
         FROM `clementine_users_treepaths` cut INNER JOIN 
              `clementine_users` cu ON cut.`descendant` = cu.`is_alias_of`
        WHERE depth > 0;"
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

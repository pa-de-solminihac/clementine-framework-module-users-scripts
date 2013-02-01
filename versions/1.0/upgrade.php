<?php
/**
 * Script non interactif d'installation du module utilisateurs
 */

// deja appele par l'installer
// $db->beginTransaction();

$sql = <<<SQL

-- -----------------------------------------------------
-- Table `clementine_users_groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `clementine_users_groups` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `group` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$sql = <<<SQL

-- -----------------------------------------------------
-- Table `clementine_users_privileges`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `clementine_users_privileges` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `privilege` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$sql = <<<SQL

-- -----------------------------------------------------
-- Table `clementine_users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `clementine_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `login` VARCHAR(128) NULL ,
  `password` VARCHAR(128) NULL ,
  `salt` VARCHAR(64) NULL ,
  `code_confirmation` VARCHAR(64) NULL ,
  `date_modification` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_creation` TIMESTAMP NULL ,
  `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `login_UNIQUE` (`login` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$sql = <<<SQL

-- -----------------------------------------------------
-- Table `clementine_users_has_groups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `clementine_users_has_groups` (
  `user_id` INT UNSIGNED NOT NULL ,
  `group_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`user_id`, `group_id`) ,
  INDEX `fk_clementine_users_groups_clementine_users` (`user_id` ASC) ,
  INDEX `fk_clementine_users_groups_clementine_users_groups1` (`group_id` ASC) ,
  CONSTRAINT `fk_clementine_users_groups_clementine_users`
    FOREIGN KEY (`user_id` )
    REFERENCES `clementine_users` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_clementine_users_groups_clementine_users_groups1`
    FOREIGN KEY (`group_id` )
    REFERENCES `clementine_users_groups` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$sql = <<<SQL

-- -----------------------------------------------------
-- Table `clementine_users_groups_has_privileges`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `clementine_users_groups_has_privileges` (
  `group_id` INT UNSIGNED NOT NULL ,
  `privilege_id` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`group_id`, `privilege_id`) ,
  INDEX `fk_clementine_users_groups_has_privileges_groups1` (`group_id` ASC) ,
  INDEX `fk_clementine_users_groups_has_privileges_privileges1` (`privilege_id` ASC) ,
  CONSTRAINT `fk_clementine_users_groups_has_privileges_groups1`
    FOREIGN KEY (`group_id` )
    REFERENCES `clementine_users_groups` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_clementine_users_groups_has_privileges_privileges1`
    FOREIGN KEY (`privilege_id` )
    REFERENCES `clementine_users_privileges` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$db->commit();
$db->beginTransaction();

$sql = <<<SQL

-- -----------------------------------------------------
-- Data for table `clementine_users_groups`
-- -----------------------------------------------------
INSERT INTO `clementine_users_groups` (`id`, `group`) VALUES (1, 'administrateurs');
INSERT INTO `clementine_users_groups` (`id`, `group`) VALUES (2, 'clients');

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$db->commit();
$db->beginTransaction();

$sql = <<<SQL

-- -----------------------------------------------------
-- Data for table `clementine_users_privileges`
-- -----------------------------------------------------
INSERT INTO `clementine_users_privileges` (`id`, `privilege`) VALUES (1, 'manage_users');
INSERT INTO `clementine_users_privileges` (`id`, `privilege`) VALUES (2, 'manage_contents');
INSERT INTO `clementine_users_privileges` (`id`, `privilege`) VALUES (3, 'manage_pages');
INSERT INTO `clementine_users_privileges` (`id`, `privilege`) VALUES (4, 'manage_commands');

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$db->commit();
$db->beginTransaction();

$sql = <<<SQL

-- -----------------------------------------------------
-- Data for table `clementine_users`
-- -----------------------------------------------------
INSERT INTO `clementine_users` (`id`, `login`, `password`, `salt`, `code_confirmation`, `date_modification`, `date_creation`, `active`) VALUES (1, 'pa@quai13.com', '49c7508c0b3be3c51615bbaa6a466a1c4d93315bbf012765825b244641b45ae0', '65ecc0658e016a99244a376b73bc12cfc8f6367201986bb52bb72b6f36fcf320', '7cc731d4b9fc752c687899c00b240b611ef96ae5fa20cbe74cabfa6f0ed06cf7', '2010-05-12 15:54:39', '2010-05-12 15:54:39', '1');

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$db->commit();
$db->beginTransaction();

$sql = <<<SQL

-- -----------------------------------------------------
-- Data for table `clementine_users_has_groups`
-- -----------------------------------------------------
INSERT INTO `clementine_users_has_groups` (`user_id`, `group_id`) VALUES (1, 1);

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

$db->commit();
$db->beginTransaction();

$sql = <<<SQL

-- -----------------------------------------------------
-- Data for table `clementine_users_groups_has_privileges`
-- -----------------------------------------------------
INSERT INTO `clementine_users_groups_has_privileges` (`group_id`, `privilege_id`) VALUES (1, 1);
INSERT INTO `clementine_users_groups_has_privileges` (`group_id`, `privilege_id`) VALUES (1, 2);
INSERT INTO `clementine_users_groups_has_privileges` (`group_id`, `privilege_id`) VALUES (1, 3);
INSERT INTO `clementine_users_groups_has_privileges` (`group_id`, `privilege_id`) VALUES (1, 4);

SQL;

if (!$db->prepare($sql)->execute()) {
    $db->rollBack();
    return false;
}

// deja appele par l'installer
// $db->commit();

// recupere la configuration du site pour savoir si le site est multilingue
if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
    $site_config = parse_ini_file('../app/local/site/etc/config.ini', true, INI_SCANNER_RAW);
} else {
    $site_config = parse_ini_file('../app/local/site/etc/config.ini', true);
}
$url_interface = '../users';
if (isset($site_config['clementine_global']['lang']) && strpos(',', $site_config['clementine_global']['lang'])) {
    $langue = preg_replace('/,.*/', '', $site_config['clementine_global']['lang']);
    if (!$langue) {
        $langue = 'fr';
    }
    $url_interface = '../' . $langue . '/users';
}

?>
<pre>
<strong>Création du premier utilisateur</strong>
<a href="<?php echo $url_interface; ?>" target="_blank">Interface de gestion des utilisateurs</a>
Nom d'utilisateur : pa@quai13.com
Mot de passe : pa13
</pre>

<?php
return true;
?>

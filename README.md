
# Oeilglauque.fr

Site web du Festival de l’Œil Glauque

## Installation

Pour installer le site localement, il faut au préalable avoir installé PHP7, Composer et MariaDB. L'installation varie selon la distribution mais est normalement relativement simple. 

 ### Exemple d'installation pour Fedora 28 : 

```bash
sudo dnf install php-cli php-common php-pdo_mysql php-gmp composer mariadb-server
sudo systemctl start mariadb
mysql --user root --execute "select version()" # Vérifier l'installation de MariaDB
mysql_secure_installation # Pour finaliser et sécuriser
```

 ### Cas particulier d'Ubuntu (18.04)

```bash
sudo apt-get install php-cli php-common php7.3-gmp php7.3-mysql php7.3-mbstring php7.3-xml composer
 # Installation de la dernière version de MariaDB, les dépots Debian sont rarement à jour
sudo apt-get install software-properties-common
sudo apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xF1656F24C74CD1D8
sudo add-apt-repository 'deb [arch=amd64] http://mirror.zol.co.zw/mariadb/repo/10.3/ubuntu bionic main'
sudo apt update
sudo apt -y install mariadb-server mariadb-client
# Vérifier l'installation de MariaDB
mysql -u root -p
select version();
```
 ### Cas particulier de Windows

* Télécharger et dézipper dans le dossier de votre choix la dernière version de php pour windows sur [windows.php.net](windows.php.net)
* Ajouter le dossier de php à la variable d'environnement PATH (voir [PATH](#Mettre-à-jour-PATH))
* Pour vérifier l'installation : créer un fichier test.php contenant `<?php echo phpinfo(); ?>`, l'exécuter depuis un terminal. La version de php doit s'afficher.
* Dans le fichier `php.ini`, décommenter les lignes `extension=gmp` et `extension=pdo_mysql`
* Télécharger et installer Composer avec l'exécutable disponible sur [getcomposer.org](getcomposer.org).
* Depuis un terminal, `composer --version` pour vérifier l'installation.
* Télécharger et installer Symfony avec l'exécutable disponible sur [symfony.com](symfony.com).
* Depuis un terminal, `symfony check:requirements` pour vérifier l'installation.
* Télécharger et installer MariaDB avec l'exécutable disponible sur [downloads.mariadb.org](downloads.mariadb.org). N'oublier pas de renseigner un mot de passe. Mettre à jour PATH.
* Depuis un terminal, `mysql -u root -p --execute "select version()"` pour vérifier l'installation.

#### Mettre à jour PATH

Dans *Panneau de configuration > Système et sécurité > Système > Paramètres système avancés > Variables d'environnement*, sélectionner la variable `Path` parmi les variables système et cliquer sur modifier. Cliquer sur nouveau puis parcourir et sélectionner le dossier contenant l'exécutable de votre choix. Valider avec OK.
Pour que le changement soit effectif, redémarrer explorer.exe (merci windows😣).
Certains installateurs modifient Path par eux-même. Néanmoins, il reste nécessaire de redémarrer explorer.exe.


### Suite de l'installation

Une fois le git cloné, il faut faire une copie de `.env.dist` en `.env`. Ensuite, on peut configurer localement le connecteur MariaDB dans le fichier `.env` selon votre installation :

```bash
DATABASE_URL=mysql://user:password@127.0.0.1:3306/databaseName
 # Remplacer user et password par ce que vous avez rempli lors de l'installation de MariaDB
 # Remplacer databaseName par le nom que vous voulez donner à la base de donnée
```

Il ne reste plus qu'à installer les dépendances, effectuer une migration de la base de données et lancer le serveur de développement : 

```bash
composer install
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
php bin/console server:run
# Ajouter --no-interaction à une commande si cette dernière plante en posant une question
```

## Déploiement

### Nouvelle installation 

`todo`

### Mise à jour vers une nouvelle version

```bash
git pull origin master
git fetch --tags
git checkout <version tag name>
composer install
php bin/console doctrine:migrations:migrate
php bin/console cache:clear --env=prod --no-debug && chmod -R 777 var/cache
docker-compose restart php-fpm
```

### Mise à jour du mot de passe de la base de données

 * Éditer la variable d'environnement MYSQL_ROOT_PASSWORD du container de base de données
 * Mettre à jour le mot de passe de l'instance actuelle :

```bash
docker exec -it web_fog-db_1 mysql -u root -p
<ancien mot de passe>
ALTER USER 'root'@'localhost' IDENTIFIED BY 'newpassword';
ALTER USER 'root'@'%' IDENTIFIED BY 'newpassword';
Ctrl+P Ctrl+Q
```

 * Modifier le fichier .env du site Symfony
 * Mettre à jour le cache et redémarrer le process PHP :

```bash
php bin/console cache:clear --env=prod --no-debug && chmod -R 777 var/cache
docker-compose restart php-fpm(-dev)
```

## Développement

### Access control

Le contrôle d'accès est fait en yaml dans `config/packages/security.yaml`. 

### Personalisation des formulaires

Les formulaires sont rendus avec un style défini dans `config/packages/twig.yaml`. Actuellement le style `bootstrap_4_horizontal_layout.html.twig` est utilisé, mais il est possible d'écraser ce style avec des champs personnalisés dans `templates/oeilglauque/form/fields.html.twig`. 

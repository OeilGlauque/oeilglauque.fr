
# Oeilglauque.fr

Site web du Festival de l’Œil Glauque

## Installation

Pour installer le site localement, il faut au préalable avoir installé PHP7.3, Composer et MariaDB. L'installation varie selon la distribution mais est normalement relativement simple. 

 ### Exemple d'installation pour Fedora 28 : 

```bash
sudo dnf install php-cli php-common php-pdo_mysql php-gmp composer mariadb-server
sudo systemctl start mariadb
mysql --user root --execute "select version()" # Vérifier l'installation de MariaDB
mysql_secure_installation # Pour finaliser et sécuriser
```

 ### Cas particulier d'Ubuntu (18.04)

```bash
 # Installation de la dernière version de php
sudo apt install php php-cli php-common php-mysql php-mbstring php-xml
 # Installation de composer v2
sudo curl -s https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
 # MariaDB à présent
sudo apt-get install mariadb-server
sudo mysql_secure_installation
# Vérifier l'installation de MariaDB
mysql -u root -p
select version();
```
 ### Cas particulier de Windows

* Télécharger et dézipper dans le dossier de votre choix la dernière version de php pour windows sur [windows.php.net](windows.php.net)
* Ajouter le dossier de php à la variable d'environnement PATH (voir [PATH](#Mettre-à-jour-PATH))
* Depuis un terminal, `php -v"` pour vérifier l'installation. La version de php doit s'afficher.
* Dans le fichier `php.ini`, décommenter les lignes `extension=gmp` et `extension=pdo_mysql`
* Télécharger et installer Composer avec l'exécutable disponible sur [getcomposer.org](getcomposer.org). Redémarrer explorer.exe pour PATH.
* Depuis un terminal, `composer --version` pour vérifier l'installation.
* Télécharger et installer Symfony avec l'exécutable disponible sur [symfony.com](symfony.com). Redémarrer explorer.exe pour PATH.
* Depuis un terminal, `symfony check:requirements` pour vérifier l'installation.
* Télécharger et installer MariaDB avec l'exécutable disponible sur [downloads.mariadb.org](downloads.mariadb.org). N'oublier pas de renseigner un mot de passe. Mettre à jour PATH.
* Depuis un terminal, `mysql -u root -p --execute "select version()"` pour vérifier l'installation.

#### Mettre à jour PATH

Dans *Panneau de configuration > Système et sécurité > Système > Paramètres système avancés > Variables d'environnement*, sélectionner la variable `Path` parmi les variables système et cliquer sur modifier. Cliquer sur nouveau puis parcourir et sélectionner le dossier contenant l'exécutable de votre choix. Valider avec OK.
Pour que le changement soit effectif, redémarrer explorer.exe (merci windows😣).
Certains installateurs modifient Path par eux-même. Néanmoins, il reste nécessaire de redémarrer explorer.exe.


### Suite de l'installation

Une fois le git cloné, il faut faire une copie de `.env` en `.env.local`. Ensuite, on peut configurer localement le connecteur MariaDB dans le fichier `.env.local` selon votre installation. On en profite aussi pour paramétrer le système de mail :

```bash
DATABASE_URL=mysql://user:password@127.0.0.1:3306/databaseName?serverVersion=mariadb-x.x.x
 # Remplacer user et password par ce que vous avez rempli lors de l'installation de MariaDB
 # Remplacer databaseName par le nom que vous voulez donner à la base de donnée
 # Remplacer x.x.x par le numéro de version de mariadb obtenu plus haut
MAILER_ADDRESS=fogfogtest@gmail.com
 # Cette adresse gmail sert de test pour le système de mail
```

Pour paramètrer correctement les mails, il faut mettre `password: testGmail159` dans le fichier `config/packages/swiftmail.yaml`.

Il ne reste plus qu'à installer les dépendances, effectuer une migration de la base de données et lancer le serveur de développement : 

```bash
composer install
composer require symfony/flex # En cas d'erreur
php bin/console doctrine:database:create
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
php bin/console server:run
# Ajouter --no-interaction à une commande si cette dernière plante en posant une question
```

Pour remplir la base de donnée actuellement pleine de table vide, on a à disposition une sauvegarde de bdd factice que l'on peut injecter en utilisant, dans le shell MySQL, la commande `source chemin/vers/la/fogdbsample.sql`.
En particulier, les utilisateurs enregistrés sont :
- root (pwd : root)
- TheBoss (pwd : portal)
- Ours de markarth (pwd : skyrim)

## Déploiement

### Nouvelle installation 

Voir [DEPLOY.md](DEPLOY.md)

### Mise à jour vers une nouvelle version

/!\ Toujours backup avant /!\

Vérifier la config mail dans `config/packages/swiftmailer.yaml`

```bash
git pull origin master
git fetch --tags
git checkout <version tag name>
composer install --no-dev --optimize-autoloader
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
php bin/console cache:clear --env=prod --no-debug && chmod -R 777 var/cache
docker-compose restart php
```

### Mise à jour du mot de passe de la base de données

 * Éditer la variable d'environnement MYSQL_ROOT_PASSWORD du container de base de données
 * Mettre à jour le mot de passe de l'instance actuelle :

```bash
docker-compose exec -u 0 mysql mysql -u fog -p
<ancien mot de passe>
ALTER USER 'fog'@'localhost' IDENTIFIED BY 'newpassword';
ALTER USER 'fog'@'%' IDENTIFIED BY 'newpassword';
Ctrl+P Ctrl+Q
```

 * Modifier le fichier .env du site Symfony
 * Mettre à jour le cache et redémarrer le process PHP :

```bash
php bin/console cache:clear --env=prod --no-debug && chmod -R 777 var/cache
docker-compose restart php
```

## Développement

### Access control

Le contrôle d'accès est fait en yaml dans `config/packages/security.yaml`. 

### Personalisation des formulaires

Les formulaires sont rendus avec un style défini dans `config/packages/twig.yaml`. Actuellement le style `bootstrap_4_horizontal_layout.html.twig` est utilisé, mais il est possible d'écraser ce style avec des champs personnalisés dans `templates/oeilglauque/form/fields.html.twig`. 

# Oeilglauque.fr

Site web du Festival de l'OEil Glauque

## Installation

Pour installer le site localement, il faut au préalable avoir installé PHP7, Composer et MariaDB. L'installation varie selon la distribution mais est normalement triviale. 

 * Exemple d'installation pour Fedora 28 : 

```bash
sudo dnf install php-cli php-common php-pdo_mysql php-gmp composer mariadb-server
sudo systemctl start mariadb
mysql --user root --execute "select version()" # To check wether your MariaDB installation is working
mysql_secure_installation # To secure your MariaDB installation
```

Après avoir configuré localement le connecteur MariaDB dans le fichier `.env` selon votre installation, il ne reste plus qu'à installer les dépendances, effectuer une migration de la base de données et lancer le serveur de développement : 

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console server:run
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

### Note for the passwordReset branch

La réinitialisation se fait par envoi d'un mail. Pour des tests en local, aller dans le fichier .env et ajouter la ligne `MAILER_URL=gmail://username:password@localhost` (ou remplacer la ligne déjà existante)
Je ferai une methode d'envoi de mail plus propre quand j'aurai le temps.

# Oeilglauque.fr

Site web du Festival de l’Œil Glauque

## Installation

Pour installer le site localement, il faut au préalable avoir installé Docker ([version Desktop](https://docs.docker.com/get-docker/) ou [version CLI](https://docs.docker.com/engine/install/)) et [Docker Compose](https://docs.docker.com/compose/install/). Pour vous faciliter la gestion des dépendances, vous pouvez également installer [Symfony CLI](https://symfony.com/download), PHP et Composer. L'installation varie selon votre système d'exploitation, mais est normalement relativement simple. Si vous êtes sur Windows il est conseiller d'ajouter Symfony CLI, PHP et Composer à votre [PATH](#mettre-à-jour-path).


#### Mettre à jour PATH sur Windows

Dans *Panneau de configuration > Système et sécurité > Système > Paramètres système avancés > Variables d'environnement*, sélectionner la variable `Path` parmi les variables système et cliquer sur modifier. Cliquer sur nouveau puis parcourir et sélectionner le dossier contenant l'exécutable de votre choix. Valider avec OK.
Pour que le changement soit effectif, redémarrer explorer.exe (merci windows😣).
Certains installateurs modifient Path par eux-même. Néanmoins, il reste nécessaire de redémarrer explorer.exe.


### Suite de l'installation

Une fois le git cloné `git clone ...`, il faut faire une copie de `.env` en `.env.local` et créer un fichier `docker-compose.override.yaml`.
Le fichier `docker-compose.override.yaml` doit avoir cette structure :

```yaml
services:
  mysql:
    environment:
      MYSQL_ROOT_PASSWORD: ROOT_PWD # à remplacer
      MYSQL_USER: USER # à remplacer
      MYSQL_PASSWORD: USER_PWD # à remplacer
      MYSQL_DATABASE: DB_NAME # à remplacer

  mailer:
    image: schickling/mailcatcher
    ports: 
      - "1025:1025"
      - "1080:1080"
    
  caddy:
    volumes:
      - ./docker/caddy/Caddyfile.dev:/etc/caddy/Caddyfile
```

Penser à bien remplacer les informations pour la base de données mysql.

Ensuite, on peut configurer localement le connecteur MariaDB et le système de mail dans le fichier `.env.local` selon votre installation. Pour les mails, vous pouvez soit utiliser le mailcatcher (plus simple) soit utiliser l'api gmail. Peu importe la méthode choisie il faut également définir une adresse mail et un nom lié à cette adresse.

```bash
DATABASE_URL=mysql://user:password@mysql/databaseName?serverVersion=mariadb-x.x.x
# Remplacer user, password et databaseName par ce que vous avez rempli dans les variables d'environnement du docker-compose.override.yaml
# Remplacer x.x.x par le numéro de version de mariadb indiquer dans le docker-compose.yaml

# Pour les mails
ADDRESS_MAIL=john.doe@exemple.com
ADRESS_NAME="John Doe"

# mail catcher
MAILER_ADDRESS=smtp://mailer:1025

# api gmail
GOOGLE_API_KEY=
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_AUTH_CONFIG=path_to_client_secret.json
```

Si vous souhaitez utiliser un webhook Discord, vous devez renseigner l'url du webhook dans la variable `DISCORD_WEBHOOK`.

Il ne reste plus qu'à installer les dépendances, créer et effectuer une migration de la base de données : 

```bash
docker compose up -d
docker compose exec -it php sh # entrer le docker php
composer install
composer require symfony/flex # En cas d'erreur
php bin/console doctrine:database:create
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

Pour remplir la base de donnée actuellement pleine de table vide, on a à disposition une sauvegarde factice de base de données :
```bash
# copier la sauvegarde dans le docker mysql
docker compose cp ./fogdbsample.sql mysql:/fogdbsample.sql
# entrer dans le docker mysql
docker compose exec -it mysql sh
mysql -u root -p # le mot de passe demander est celui que vous avez mis dans la variable MYSQL_ROOT_PASSWORD du docker-compose.override.yaml
```
```SQL
use DB_NAME; #DB_NAME correspond à ce que vous avez mis dans le docker-compose.override.yaml

source fogdbsample.sql;
quit;
```

En particulier, les utilisateurs enregistrés sont :
- root (pwd : root)
- TheBoss (pwd : portal)
- Ours de markarth (pwd : skyrim)

On peut maintenant lancer le site !
```
docker compose up -d
```

## Déploiement

Voir [DEPLOY.md](DEPLOY.md)

## Développement

### Access control

Le contrôle d'accès est fait en yaml dans `config/packages/security.yaml`. 

### Personalisation des formulaires

Les formulaires sont rendus avec un style défini dans `config/packages/twig.yaml`. Actuellement le style `bootstrap_4_horizontal_layout.html.twig` est utilisé, mais il est possible d'écraser ce style avec des champs personnalisés dans `templates/oeilglauque/form/fields.html.twig`. 

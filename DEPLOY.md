# Nouvelle installation

## Préparer la machine d'hébergement

Il faut installer Docker et Docker Compose et autoriser l'utilisateur courant à les utiliser. 
```bash
sudo groupadd docker # The docker group might already exists
sudo usermod -aG docker $USER
newgrp docker
docker ps # For testing purpose
```
L'utilisation de [lazydocker](https://github.com/jesseduffield/lazydocker) est fortement recommandée.

Sélectionner un dossier et cloné le repository dans ce dernier.

## Configuration de l'environnement

Une fois le git cloné, il faut faire une copie de `.env` en `.env.local` et créer un fichier `docker-compose.override.yaml`.
Le fichier `docker-compose.override.yaml` doit avoir cette structure :

```yaml
services:
  mysql:
    environment:
      MYSQL_ROOT_PASSWORD: ROOT_PWD # à remplacer
      MYSQL_USER: USER # à remplacer
      MYSQL_PASSWORD: USER_PWD # à remplacer
      MYSQL_DATABASE: DB_NAME # à remplacer
```

Penser à bien remplacer les informations pour la base de données mysql.

Ensuite, il faut configurer localement le connecteur MariaDB et le système de mail dans le fichier `.env.local`.

```bash
APP_ENV=prod

DATABASE_URL=mysql://user:password@mysql/databaseName?serverVersion=mariadb-x.x.x
# Remplacer user, password et databaseName par ce que vous avez rempli dans les variables d'environnement du docker-compose.override.yaml
# Remplacer x.x.x par le numéro de version de mariadb indiquer dans le docker-compose.yaml

# Pour les mails
ADDRESS_MAIL=john.doe@exemple.com
ADRESS_NAME="John Doe"

# api gmail
GOOGLE_API_KEY=
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_AUTH_CONFIG=path_to_client_secret.json
```

Si vous souhaitez utiliser un webhook Discord, vous devez renseigner l'url du webhook dans la variable `DISCORD_WEBHOOK`.

## Premier lancement des dockers

Il ne reste plus qu'à installer les dépendances, créer et effectuer une migration de la base de données : 

```bash
docker compose up -d
docker compose exec -it php sh # entrer le docker php
composer install --no-dev --optimize-autoloader
composer require symfony/flex # En cas d'erreur
php bin/console doctrine:database:create
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

# Mise à jour vers une nouvelle version

/!\ Toujours backup avant /!\


```bash
git pull origin master
# si vous utiliser des tags
git fetch --tags
git checkout <version tag name>
docker compose exec -it php sh # entrer dans le docker php
composer install --no-dev --optimize-autoloader # si les dépendances ont changées
php bin/console doctrine:migrations:diff # si vous avez modifié ou créé des entités
php bin/console doctrine:migrations:migrate # si vous avez modifié ou créé des entités
php bin/console cache:clear
```

### Mise à jour du mot de passe de la base de données

 * Éditer la variable d'environnement MYSQL_ROOT_PASSWORD du container de base de données
 * Mettre à jour le mot de passe de l'instance actuelle :

```bash
docker compose exec -it mysql sh
mysql -u DB_NAME -p # le mot de passe demander est l'ancien mot de passe
ALTER USER 'DB_NAME'@'localhost' IDENTIFIED BY 'newpassword';
ALTER USER 'DB_NAME'@'%' IDENTIFIED BY 'newpassword';
Ctrl+P Ctrl+Q
```

 * Modifier le fichier .env.local du site Symfony
 * Mettre à jour le cache et redémarrer le process PHP :

```bash
docker compose exec php sh -c "php bin/console cache:clear"
docker compose restart php
```

# Quelques commandes MySQL utiles

- Faire une backup de la base de donnée
```bash
mysqldump -u root -p fogdb -r fogbackup.sql
```
Ajouter `-r` sur Windows.

- Charger une backup de la base de donnée
```sql
use fogdb;
source fogbackup.sql
```

- Charger une table avec un csv
```sql
use fogdb
load data local infile 'tableData.csv' into table tableName fields terminated by ';' lines terminated by '\n';
```

- Copier un fichier depuis un container
```bash
docker cp nom_du_container:/path/vers/le/fichier /path/vers/la/destination
```

Utiliser `docker cp` au besoin pour transporter les fichiers entre host et container.

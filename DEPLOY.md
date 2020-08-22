# Nouvelle installation

Basée sur [how to dockerise a symfony project](https://knplabs.com/fr/blog/how-to-dockerise-a-symfony-4-project).

## Préparer la machine d'hébergement
Il faut installer `docker` et `docker-compose` et autoriser l'utilisateur courant à les utiliser. 
```bash
sudo groupadd docker //The docker group might already exists
sudo usermod -aG docker $USER
newgrp docker
docker ps //For testing purpose
```
L'utilisation de [lazydocker](https://github.com/jesseduffield/lazydocker) est fortement recommandée.

L'architecture du dossier d'installation doit être comme suit :
```
 - apps/
   - oeilglauque.fr/
 - bin/
 - docker/
   - nginx/
       - default.conf
   - php/
       - Dockerfile
   - db/
   - certbot/
 - docker-compose.yml 
```

## Lancer les containers mariadb et nginx

Le fichier docker-compose.yml ressemblera à cela d'ici la fin du déploiement :

```yml
version: '3.5'
services:
    mysql:
        image: mariadb:10.5.3
        restart: on-failure
        environment:
            MYSQL_ROOT_PASSWORD: rootpwd
            MYSQL_USER: fog
            MYSQL_PASSWORD: fogpwd
            MYSQL_DATABASE: fogdb
        ports: 
          - '3306:3306'
        volumes: 
          - './docker/db:/var/lib/mysql'

    adminer:
        image: adminer
        restart: on-failure
        ports:
          - '8080:8080'

    nginx:
        image: nginx:1.19.0-alpine
        restart: on-failure
        volumes:
          - './apps/oeilglauque.fr/public/:/usr/src/app'
          - './docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro'
          - './docker/nginx/log/:/var/log/nginx/'
          - './docker/certbot/conf:/etc/letsencrypt'
          - './docker/certbot/www:/var/www/certbot'
        ports:
          - '80:80'
          - '443:443'
        command: "/bin/sh -c 'while :; do sleep 6h & wait $${!}; nginx -s reload; done & nginx -g \"daemon off;\"'"
        depends_on:
             - php

    certbot: 
        image: certbot/certbot 
        restart: always
        volumes: 
          - './docker/certbot/conf:/etc/letsencrypt' 
          - './docker/certbot/www:/var/www/certbot' 
        entrypoint: "/bin/sh -c 'trap exit TERM; while :; do certbot renew; sleep 12h & wait $${!}; done;'"

    php:
        build:
          context: .
          dockerfile: docker/php/Dockerfile
        restart: always
        user: 1000:1000
```

La version du fichier dépends de la version de docker mais ubuntu peut être un peu capricieux. La version stable la plus récente fera l'affaire. Pour mariadb, on choisira l'image stable la plus récente, idem pour nginx en version alpine. Adminer n'est là que pour pouvoir monitorer facilement la bdd et n'est pas absolument nécessaire.  Certbot permet d'obtenir des certificats let'sencrypt automatiquement.
On précise les options dont on a envie pour mysql, avec de meilleurs mots de passe bien sûr. Si nécessaire, on stop tout les autres services écoutant sur les port 80 et 443.

Le fichier de configuration nginx est le suivant :
```nginx
server { 
	listen 80;
	server_name oeilglauque.fr; 
	
	location / { 
		return 301 https://$host$request_uri; 
	} 
	location /.well-known/acme-challenge/ { 
		root /var/www/certbot; 
	} 
}

server {
	listen 443 ssl; 
	server_name oeilglauque.fr, www.oeilglauque.fr;
	ssl_certificate /etc/letsencrypt/live/oeilglauque.fr/fullchain.pem; 
	ssl_certificate_key /etc/letsencrypt/live/oeilglauque.fr/privkey.pem; 
	include /etc/letsencrypt/options-ssl-nginx.conf; 
	ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem; 

	location / { 
		root /usr/src/app; 
		try_files $uri /index.php$is_args$args; 
	}
	location ~ ^/index\.php(/|$) {
		client_max_body_size 50m; 
		fastcgi_pass php:9000; 
		fastcgi_buffers 16 16k;
		fastcgi_buffer_size 32k; 
		include fastcgi_params; 
		fastcgi_param SCRIPT_FILENAME /usr/src/app/public/index.php; 
	}
	location ~ \.php$ { 
		return 404; 
	} 

	error_log /var/log/nginx/error.log; 
	access_log /var/log/nginx/access.log;
}
```
Il y a en réalité 2 serveurs nginx. Un écoute le http et renvoie vers le https ou résout les challenges ssl. Le second écoute en https et sert le site.
On peut, dans un premier temps, tester en enlevant les références à php/https et en modifiant la config nginx pour du http seulement. `sudo docker-compose up -d` pour lancer les containers. Normalement, les localhost :80 et :8080 doivent afficher les interfaces nginx et adminer et on peut se connecter à la bdd via ce dernier.

## Préparer le container php

On écrit le Dockerfile :
```docker
# ./docker/php/Dockerfile
FROM php:7.4-fpm

RUN docker-php-ext-install pdo_mysql

RUN pecl install apcu

RUN apt-get update && \
apt-get install -y \
zlib1g-dev \
libzip-dev \
libgmp-dev \
libxml2-dev

RUN docker-php-ext-configure gmp
RUN docker-php-ext-install gmp
RUN docker-php-ext-configure xml
RUN docker-php-ext-install xml
RUN docker-php-ext-install zip
RUN docker-php-ext-enable apcu

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && mv composer /usr/local/bin/composer

WORKDIR /usr/src/app

RUN PATH=$PATH:/usr/src/apps/vendor/bin:bin
```
On utilise la version de php-fpm stable la plus récente.
Il faut absolument préparer le `.env.local` correctement dans le projet. Notamment :
 - `APP_ENV=prod`
 - `DATABASE_URL=mysql://fog:fogpwd@mysql:3306/fogdb?serverVersion=mariadb-10.5.3` (La version mariadb dépend de l'install)
 - `MAILER_URL=gmail://emailfog:password@localhost`
 - `MAILER_ADDRESS=emailfog`

Ensuite, il faut build le Dockerfile: `sudo docker-compose build` et relancer les containers `sudo docker-compose up -d` en s'assurant que la définition du container php soit bien présent dans le docker-compose.yml.

En principe avec la commande exec de docker on peut lancer l'install de composer mais ce n'est pas toujours possible. On peut directement entrer dans le bash du container: `sudo docker-compose exec -u 0 php /bin/bash`(ou directement via lazydocker)

On peut finir l'installation :
```bash
composer install --no-dev --optimize-autoloader
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```
## Mettre en place le https

Cette partie se base sur [cet article](https://medium.com/@pentacent/nginx-and-lets-encrypt-with-docker-in-less-than-5-minutes-b4b8a60d3a71).

On peut cette fois-ci utiliser la config finale. Pour pouvoir lancer nginx en https il faut faire une manip un peu complexe qui est automatisée par un script. On récupère donc le script en question et on l'édite avec les bons paramètres (domaines avec et sans www, adresse email et `data_path="./docker/certbot"`). 

```bash
curl -L https://raw.githubusercontent.com/wmnnd/nginx-certbot/master/init-letsencrypt.sh -o init-letsencrypt.sh
```
Dans un premier temps, pour tester, on peut mettre staging à 1 afin de ne pas épuiser le quota de certificat/site/semaine de let'sencrypt en cas de problème. Dans ce cas le certificat sera bien présent, mais ne sera pas trusté par les navigateurs.
Ensuite, on peut exécuter le script.
```bash
chmod +x init-letsencrypt.sh
sudo ./init-letsencrypt.sh
```
Si tout se passe bien, certbot nous félicite, on peut accéder au site en https et le http redirige vers le https. On peut alors le faire définitivement avec staging à 0. Cette opération n'est à faire qu'une fois pour initialiser les certificats. Ces derniers sont normalement renouvelés tous les ~90 jours.

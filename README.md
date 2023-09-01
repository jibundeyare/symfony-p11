# Symfony P11

Ce repo contient une application de gestion de formation.
Il s'agit d'un projet pédagogique pour la promo 11.

## Prérequis

- Linux, MacOS ou Windows
- Bash
- PHP 8
- Composer
- Symfony-cli
- MariaDB 10
- Docker (optionnel)

## Installation

```
git clone https://github.com/jibundeyare/symfony-p11
cd symfony-p11
composer install
```

Créez une base de données et un utilisateur dédié pour cette base de données.

## Configuration

Créez un fichier `.env.local` à la racine du projet :

```
APP_ENV=dev
APP_DEBUG=true
APP_SECRET=c4bcab84467682d84ed65a86648c7747
DATABASE_URL="mysql://symfony_p11:123@127.0.0.1:3306/symfony_p11?serverVersion=mariadb-10.6.12&charset=utf8mb4"
```

Pensez à changer la variable `APP_SECRET` et les codes d'accès dans la variable `DATABASE_URL`.

**ATTENTION : `APP_SECRET` doit être une chaîne de caractère de 32 caractères en hexadecimal.**

## Migration et fixtures

Pour que l'application soit utilisable, vous devez créer le schéma de base de données et charger les données :

```
bin/dofilo.sh
```

## Utilisation

Lancez le serveur web de développement :

```
symfony serve
```

Puis ouvrez la page suivante : [https://localhost:8000](https://localhost:8000).

## Mentions légales

Sous projet est sous licence MIT.

La licence est disponible ici : [MIT LICENCE](LICENCE).

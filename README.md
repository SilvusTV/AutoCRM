# Auto-CRM Freelance

Auto-CRM Freelance est une application web conçue pour aider les freelances à gérer leur activité professionnelle. Elle
permet de suivre les clients, les projets, le temps passé, les factures et les déclarations URSSAF.

![Laravel](https://img.shields.io/badge/Laravel-12.0-red)
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![License](https://img.shields.io/badge/License-MIT-green)

## Fonctionnalités

### Gestion des clients
- Création et gestion des fiches clients
- Suivi de l'historique des projets et factures par client

### Gestion des projets
- Création et suivi des projets
- Association des projets aux clients
- Suivi du statut des projets (en cours, terminé, archivé)

### Suivi du temps
- Enregistrement du temps passé sur chaque projet
- Filtrage des entrées de temps par date, projet, client
- Vue calendrier pour visualiser le temps passé
- Export CSV des données de temps

### Facturation
- Création de factures liées aux projets
- Gestion des lignes de facturation
- Suivi du statut des factures (brouillon, envoyée, payée)
- Génération de PDF pour les factures

### Déclarations URSSAF
- Création et suivi des déclarations URSSAF
- Calcul automatique des charges basé sur le revenu déclaré
- Suivi des paiements des charges sociales

### Tableau de bord
- Vue d'ensemble de l'activité
- Statistiques financières (revenus, charges)
- Accès rapide aux fonctionnalités principales

## Installation

### Prérequis
- PHP 8.2 ou supérieur
- Composer
- MySQL ou MariaDB
- Node.js et NPM

### Installation avec Docker

1. Clonez le dépôt
```bash
git clone https://github.com/votre-utilisateur/auto-crm-freelance.git
cd auto-crm-freelance
```

2. Copiez le fichier d'environnement
```bash
cp .env.example .env
```

3. Lancez les conteneurs Docker
```bash
docker-compose up -d
```

4. Installez les dépendances et générez la clé
```bash
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app npm install
docker-compose exec app npm run build
```

5. Accédez à l'application à l'adresse http://localhost:8000

### Installation manuelle

1. Clonez le dépôt
```bash
git clone https://github.com/votre-utilisateur/auto-crm-freelance.git
cd auto-crm-freelance
```

2. Installez les dépendances
```bash
composer install
npm install
```

3. Copiez le fichier d'environnement et configurez-le
```bash
cp .env.example .env
```

4. Générez la clé d'application
```bash
php artisan key:generate
```

5. Configurez votre base de données dans le fichier .env

6. Exécutez les migrations
```bash
php artisan migrate
```

7. Compilez les assets
```bash
npm run build
```

8. Lancez le serveur
```bash
php artisan serve
```

9. Accédez à l'application à l'adresse http://localhost:8000

## Tests

Pour exécuter les tests, utilisez la commande suivante :
```bash
php artisan test
```

## Qualité du code

Ce projet utilise [Laravel Pint](https://laravel.com/docs/10.x/pint) pour maintenir un style de code cohérent selon les
standards Laravel.

### Vérification manuelle du style de code

Pour vérifier le style de code sans appliquer les corrections :

```bash
vendor/bin/pint --test
```

Pour appliquer automatiquement les corrections de style :

```bash
vendor/bin/pint
```

### Pre-commit Hook avec Husky

Ce projet utilise [Husky](https://typicode.github.io/husky/) pour exécuter automatiquement Laravel Pint sur les fichiers
PHP modifiés avant chaque commit. Cela garantit que tout le code commité respecte les standards de codage Laravel.

Pour configurer le hook pre-commit :

```bash
# Installer les dépendances npm si ce n'est pas déjà fait
npm install

# Configurer Husky (gestionnaire de hooks Git)
npm run prepare
```

Pour plus d'informations sur les hooks Git, consultez le [README dans le dossier .husky](.husky/README.md).

### Utilisation sans Husky

Si vous préférez ne pas utiliser Husky et les hooks Git, vous pouvez simplement exécuter Laravel Pint manuellement avant
chaque commit. Voici comment procéder :

1. Vérifiez les fichiers modifiés qui seront inclus dans votre commit :

```bash
git status
```

2. Exécutez Laravel Pint pour corriger automatiquement les problèmes de style :

```bash
# Utiliser la commande directement sur tous les fichiers PHP
vendor/bin/pint

# Ou utiliser le script npm défini dans package.json (tous les fichiers)
npm run lint

# Pour n'appliquer les corrections qu'aux fichiers PHP modifiés (plus efficace)
# Sur Windows
npm run lint:changed

# Sur Linux/macOS
npm run lint:changed:unix
```

3. Ajoutez les fichiers corrigés à votre commit :

```bash
git add .
```

4. Effectuez votre commit normalement :

```bash
git commit -m "Votre message de commit"
```

Cette approche manuelle vous donne plus de contrôle sur le moment où les corrections de style sont appliquées, sans
ajouter de complexité avec les hooks Git.

#### Commande tout-en-un

Pour simplifier encore plus le processus, vous pouvez utiliser une commande qui combine toutes les étapes (correction du
style, ajout des fichiers et commit) :

```bash
# Sur Windows - remplacez "Votre message de commit" par votre message réel
npm run lint:commit "Votre message de commit"

# Sur Linux/macOS - remplacez "Votre message de commit" par votre message réel
npm run lint:commit:unix "Votre message de commit"
```

Ces commandes vont automatiquement :

1. Corriger les problèmes de style dans les fichiers PHP modifiés
2. Ajouter les fichiers corrigés à votre commit
3. Effectuer le commit avec le message que vous avez spécifié

## Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

# Mini-CRM Freelance

Mini-CRM Freelance est une application web conçue pour aider les freelances à gérer leur activité professionnelle. Elle permet de suivre les clients, les projets, le temps passé, les factures et les déclarations URSSAF.

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
git clone https://github.com/votre-utilisateur/mini-crm-freelance.git
cd mini-crm-freelance
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
git clone https://github.com/votre-utilisateur/mini-crm-freelance.git
cd mini-crm-freelance
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

## Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

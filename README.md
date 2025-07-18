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
- Suivi du statut des factures (draft, sent, paid, cancelled, overdue)
- Génération de PDF pour les factures

### Déclarations URSSAF
- Création et suivi des déclarations URSSAF
- Calcul automatique des charges basé sur le revenu déclaré
- Suivi des paiements des charges sociales

### Profil amélioré

- Interface avec navigation latérale
- Section Profil pour les informations personnelles
- Section URSSAF pour configurer le rythme de déclaration et le niveau d'imposition
- Section Entreprise pour gérer les informations de l'entreprise et télécharger un logo
- Section Moyens de paiement pour gérer les comptes bancaires et les méthodes de paiement (Stripe, PayPal)

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

### Installation avec Docker (Développement)

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

### Déploiement en production avec Docker

Pour déployer l'application en production, nous avons créé un Dockerfile spécifique qui automatise l'installation des
dépendances, les migrations et la compilation des assets.

1. Clonez le dépôt

```bash
git clone https://github.com/votre-utilisateur/auto-crm-freelance.git
cd auto-crm-freelance
```

2. Copiez et configurez le fichier d'environnement pour la production

```bash
cp .env.example .env
# Éditez le fichier .env pour configurer les paramètres de production
# Assurez-vous de définir APP_ENV=production et APP_DEBUG=false
```

3. Lancez les conteneurs Docker pour la production

```bash
docker-compose -f docker-compose-production.yml up -d
```

4. Créez un lien symbolique pour le stockage (si nécessaire)

```bash
docker-compose -f docker-compose-production.yml exec app php artisan storage:link
```

5. Accédez à l'application à l'adresse de votre serveur

Le Dockerfile de production effectue automatiquement les opérations suivantes :

- Installation des dépendances PHP avec Composer (optimisées pour la production)
- Installation des dépendances Node.js et compilation des assets
- Configuration des permissions appropriées
- Exécution des migrations de base de données au démarrage du conteneur

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

## Configuration du stockage S3 pour les logos d'entreprise

L'application utilise Scaleway S3 pour stocker les logos d'entreprise. Pour configurer le stockage S3, ajoutez les
variables suivantes à votre fichier `.env` :

```
AWS_ACCESS_KEY_ID=votre_cle_acces_scaleway
AWS_SECRET_ACCESS_KEY=votre_cle_secrete_scaleway
AWS_DEFAULT_REGION=fr-par
AWS_BUCKET=nom_de_votre_bucket
AWS_ENDPOINT=https://s3.fr-par.scw.cloud
AWS_URL=https://s3.fr-par.scw.cloud/nom_de_votre_bucket
AWS_USE_PATH_STYLE_ENDPOINT=false
```

Les logos téléchargés sont automatiquement :

- Renommés avec un UUID unique
- Convertis au format WebP pour optimiser la taille
- Stockés dans le dossier 'company_logo' du bucket S3

## Configuration de l'email pour la production

Par défaut, l'application utilise Mailpit pour les emails en environnement de développement. Pour la production, vous
pouvez utiliser [Resend](https://resend.com), un service d'envoi d'emails moderne et fiable.

### Configuration de Resend

1. Créez un compte sur [Resend](https://resend.com) et obtenez votre clé API

2. Modifiez votre fichier `.env` en production pour utiliser Resend :

```
MAIL_MAILER=resend
RESEND_KEY=votre_cle_api_resend
MAIL_FROM_ADDRESS=votre_email@votredomaine.com
MAIL_FROM_NAME="Nom de votre application"
```

3. Assurez-vous que les emails transactionnels fonctionnent correctement en testant les fonctionnalités comme la
   réinitialisation de mot de passe ou la vérification d'email

### Autres services d'email

L'application prend également en charge d'autres services d'envoi d'emails comme Mailgun, Postmark, ou Amazon SES.
Consultez la [documentation de Laravel](https://laravel.com/docs/12.x/mail) pour plus d'informations sur la
configuration de ces services.

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

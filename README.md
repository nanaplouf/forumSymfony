# 📘 Documentation Symfony avec Docker

## 🧩 Introduction

### Qu’est-ce que Symfony ?

Symfony est un **framework PHP open source** utilisé pour développer des applications web robustes, performantes et maintenables.  
Il repose sur une architecture **MVC (Modèle – Vue – Contrôleur)** et propose un ensemble d’outils et de composants réutilisables facilitant la création rapide d’applications professionnelles.

### Pourquoi utiliser Docker avec Symfony ?

Docker permet de créer un **environnement de développement isolé et reproductible**.  
Grâce à Docker, votre application Symfony tournera de la même manière sur tous les postes (Windows, macOS, Linux) sans avoir à gérer les dépendances PHP, MySQL, etc.  
Il simplifie aussi le déploiement en production.

---

## ⚙️ Installation et Configuration

### 1. Cloner le projet de base

Pour créer un projet Symfony sur un environnement Docker, téléchargez le repository suivant :  
👉 [https://github.com/dunglas/symfony-docker/tree/main](https://github.com/dunglas/symfony-docker/tree/main)

**Commande :**

```bash
git clone https://github.com/dunglas/symfony-docker.git
```

⚠️ **Attention** : ne conservez que les fichiers relatifs à **FrankenPHP**, **Docker**, et les fichiers **.gitignore**.

_(Ici, insérer une image de l’arborescence souhaitée)_

---

### 2. Lancer le projet

Suivez la documentation et exécutez les commandes :

```bash
docker compose build --pull --no-cache
docker compose up --wait
```

Puis vérifiez sur [http://localhost](http://localhost) que la page de base de Symfony s’affiche correctement.  
_(Ici, insérer une capture d’écran de la page d’accueil)_

---

### 3. Configuration de la base de données MySQL

Par défaut, le projet utilise PostgreSQL.  
Pour le remplacer par MySQL, suivez la documentation officielle :  
👉 [docs/mysql.md](https://github.com/dunglas/symfony-docker/blob/main/docs/mysql.md)

Ensuite, installez le pack Doctrine :

```bash
docker compose exec php composer req symfony/orm-pack
```

---

### 4. Modifier le fichier `compose.yaml`

Ajoutez ou modifiez la section suivante :

```yaml
###> doctrine/doctrine-bundle ###
database:
  image: mysql:${MYSQL_VERSION:-8}
  environment:
    MYSQL_DATABASE: ${MYSQL_DATABASE:-myforum}
    MYSQL_PASSWORD: ${MYSQL_PASSWORD:-admin}
    MYSQL_USER: ${MYSQL_USER:-admin}
    MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD:-admin}
  healthcheck:
    test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
    timeout: 5s
    retries: 5
    start_period: 60s
  volumes:
    - database_data:/var/lib/mysql:rw
###< doctrine/doctrine-bundle ###
```

Et en haut du fichier :

```yaml
DATABASE_URL: mysql://${MYSQL_USER:-app}:${MYSQL_PASSWORD:-!ChangeMe!}@database:3306/${MYSQL_DATABASE:-app}?serverVersion=${MYSQL_VERSION:-8}&charset=${MYSQL_CHARSET:-utf8mb4}
```

⚠️ Pensez à **changer les mots de passe, le nom de la base de données et les informations sensibles**.

---

### 5. Autres ajustements nécessaires

- Dans `compose.override.yaml`, vérifiez le port :
  ```yaml
  3306:3306
  ```
  ou, si le port est déjà utilisé :
  ```yaml
  3307:3306
  ```
- Dans le `Dockerfile`, remplacez :
  ```bash
  RUN install-php-extensions pdo_pgsql
  ```
  par :
  ```bash
  RUN install-php-extensions pdo_mysql
  ```

---

### 6. Commandes de droits

Avant toute modification, exécutez :

```bash
sudo chown -R 1000:1000 .
chmod -R 755 .
```

🧠 **Pourquoi ces commandes ?**

- `chown -R 1000:1000 .` : attribue la propriété des fichiers à l’utilisateur Docker (ID 1000).
- `chmod -R 755 .` : donne les droits de lecture et d’exécution nécessaires aux fichiers.  
  Ces deux commandes évitent les **erreurs de permission** lors de la création de fichiers par Docker.

---

### 7. Redémarrage du projet

```bash
docker compose down --remove-orphans && docker compose build --pull --no-cache
docker compose up --wait
```

Testez la connexion à la base de données :

```bash
docker compose exec php bin/console dbal:run-sql -q "SELECT 1" && echo "OK" || echo "Connection is not working"
```

Vérifiez vos conteneurs dans **Docker Desktop** et sur **localhost**.

---

## 💻 CLI Symfony & Première Page

### Le CLI Symfony

Symfony fournit un **outil en ligne de commande (CLI)** permettant de générer du code, gérer la base de données, lancer un serveur, etc.

Pour accéder au bash dans le conteneur PHP :

```bash
docker compose exec php bash
```

Lister toutes les commandes disponibles :

```bash
php bin/console list
```

---

### Installer les bundles nécessaires

```bash
composer require symfony/maker-bundle --dev
composer require twig
composer require symfony/asset
```

🧠 **À quoi servent-ils ?**

- **MakerBundle** : permet de générer rapidement du code (contrôleurs, entités, formulaires…).
- **Twig** : moteur de templates utilisé pour afficher les vues HTML.
- **Asset** : gère les ressources front-end (CSS, images, JS).

---

### Créer un contrôleur et une vue

```bash
php bin/console make:controller ForumController
```

#### Fonction `render()`

La méthode `render()` affiche une vue Twig à l’utilisateur.

**Syntaxe :**

```php
return $this->render('chemin/vers/le/template.html.twig', [
    // Variables à passer au template
]);
```

**Explication :**

- `$this->render(...)` : génère une page HTML depuis un fichier `.twig`.
- `'forum/index.html.twig'` : chemin vers la vue.
- `forum/` : dossier dans `templates/`.
- `index.html.twig` : nom du fichier affiché.

🔎 **Commandes utiles :**

```bash
php bin/console debug:router
php bin/console list
```

---

## 🔐 Authentification

### Création de l’entité User

```bash
composer require security
php bin/console make:user
```

🧠 **Explication de `make:user` :**  
Cette commande crée une entité `User` qui représente les utilisateurs de votre application.  
Vous pouvez choisir le champ d’identifiant (email) et définir le stockage du mot de passe (hashé).

---

### Doctrine & ORM

Un **ORM (Object-Relational Mapping)** permet de manipuler la base de données via des objets PHP au lieu d’écrire des requêtes SQL.  
Symfony utilise **Doctrine** comme ORM.

Générez la migration :

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

⚠️ Vérifiez bien le port dans **Workbench** lors de la connexion à la base.

---

### Configuration du système de sécurité

Créez un formulaire de connexion :

```bash
php bin/console make:security:form-login
```

🧠 **Explication :**  
Cette commande génère tout le code nécessaire à un système de connexion classique (login form, authentificateur, routes, etc.).

---

### Création du formulaire d’enregistrement

```bash
composer require form validator
php bin/console make:registration-form
```

🧠 **Explication :**  
Cette commande crée un formulaire d’inscription basé sur l’entité `User`.  
Elle peut aussi configurer :

- la vérification par email (optionnelle),
- la connexion automatique après inscription,
- et les tests unitaires associés.

---

➡️ Une fois tout cela fait, vous pouvez personnaliser **les informations, les styles et les pages** selon votre projet.

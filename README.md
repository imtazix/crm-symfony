# CRM Symfony - Projet Dockerisé 🐳

Ce projet est une application CRM développée avec **Symfony 6+** et **Docker**.  
Il inclut un système d'authentification par **JWT (LexikJWT)**, la gestion des **utilisateurs**, **clients** et **factures**.

---

##  Lancement du projet

### 1. Cloner le dépôt

```bash
git clone https://github.com/imtazix/crm-symfony.git
cd crm-symfony
```

### 2. Lancer les conteneurs Docker

```bash
docker compose up -d --build
```

---

## 🐘 Services Docker

| Service | Description         | Port             |
|---------|---------------------|------------------|
| app     | PHP 8.2 + Apache    | `localhost:8000` |
| db      | MySQL 8.0           | `localhost:3306` |

---

##  Accès à l'application

- Accès web : [http://localhost:8000/login](http://localhost:8000/login)
- Identifiants de connexion :

```text
Identifiant : admin
Mot de passe : admin123
```

---

##  Commandes à exécuter dans le conteneur

### 1. Entrer dans le conteneur app

```bash
docker exec -it crm-symfony-app bash
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Appliquer les migrations

```bash
php bin/console doctrine:migrations:migrate
```

### 4. Charger les fixtures (admin, client, facture)

```bash
php bin/console doctrine:fixtures:load
```

---

##  API Authentification (JWT)

### Endpoint :

```http
POST http://localhost:8000/api/login
```

### Corps de la requête (JSON) :

```json
{
  "identifiant": "admin",
  "password": "admin123"
}
```

### Réponse attendue :

```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

Vous pouvez le décoder via https://jwt.io pour consulter son contenu.

---

##  Structure technique

- Symfony 6.x
- Doctrine ORM
- LexikJWTAuthenticationBundle
- Symfony Flex
- Docker & Docker Compose
- JWT stocké en session (login HTML)
- Décodage du token via `firebase/php-jwt`

---

## Objectifs atteints

- [x] Authentification API via JWT
- [x] Authentification Web avec session
- [x] Gestion des utilisateurs avec fixture
- [x] Base de données MySQL Dockerisée
- [x] Interface de login HTML avec Bootstrap

---

## 🔒 À venir (TODO)

- [ ] Sécuriser les routes (`/dashboard`, `/clients`, `/factures`)
- [ ] Création/édition de clients
- [ ] CRUD Factures
- [ ] Interface utilisateur + dashboard

---

## 🧹 Nettoyer l'environnement

```bash
docker compose down -v
```

---

## 👨‍💻 Auteur

**Mohamed Belmadani**  
Projet Examen Symfony - DevOps 2025  
GitHub : [imtazix](https://github.com/imtazix)

---

📫 Pour toute question, crée une `issue` sur le dépôt ou contacte-moi directement.
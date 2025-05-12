# CRM Symfony - Gestion des Clients et Factures

Ce projet Symfony permet de gérer des clients et leurs factures à travers une API sécurisée par JWT, ainsi qu'une interface utilisateur complète en Twig/Bootstrap.

## ✅ Technologies utilisées

* PHP 8.2
* Symfony 6+
* MySQL 8
* Docker / Docker Compose
* JWT (LexikJWTAuthenticationBundle)
* Twig + Bootstrap 5

## 🔥 Développé par

**Mohamed El Outmani**

## ⚙️ Installation via Docker

1. Clonez le projet :

```bash
git clone https://github.com/imtazix/crm-symfony.git
cd crm-symfony
```

2. Lancez Docker :

```bash
docker compose up -d --build
```

Cela va :

* Créer les conteneurs `crm-symfony-app` et `crm-symfony-db`
* Exposer l'app Symfony sur `http://localhost:8000`

3. Accédez au conteneur PHP pour lancer les commandes :

```bash
docker exec -it crm-symfony-app bash
```

4. Exécutez les migrations + fixtures :

```bash
php bin/console doctrine:migrations:migrate
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
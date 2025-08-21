# Symfony Simple Blog App

A simple blog application built with Symfony 7 and PHP 8. Users can register, confirm email, log in, write posts, and comment. Admins manage content and moderate users. Security features ensure only verified users can log in, and banned users are blocked.

**Key Features**
- User registration, email confirmation, and secure login
- Create, edit, delete posts
- Comment on posts, edit and delete own comments
- Access control: only verified users can log in; banned users blocked
- Admin moderation: delete any post/comment, ban users with warning emails

**Technologies**
- PHP 8+
- Symfony 7 
- Doctrine ORM 
- Twig templating engine
- Symfony Mailer for sending confirmation and notification emails
- Symfony Security with custom UserChecker and AppAuthenticator
- Flash messages for user feedback
- Validator constraints (Email, NotBlank, Length, Regex)
- Docker / Docker Compose
- HTML5, CSS3, JavaScript

**Installation / Setup**
1. Make sure you have Docker and Docker Compose installed.
2. Clone the repository
3. Build and start the Docker containers
```bash
docker compose up -d --build
```
4. Create the database and run migrations:
```bash
docker compose exec php php bin/console doctrine:database:create
docker compose exec php php bin/console doctrine:migrations:migrate
```
5. Visit: http://localhost:8010
6. All emails sent by the application can be viewed through the Mailer web interface at http://localhost:8025.

**Planned Feature**
- User profile editing
- Post categories and tags
- Improved admin dashboard with analytics
- Likes system to allow users to like posts

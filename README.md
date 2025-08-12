# Symfony Simple Blog App

This is a simple blog app built with Symfony 7 and PHP 8. Users can sign up, confirm their email, log in, write posts, and comment on them. The app has security features that make sure only verified users can log in, and it blocks banned users from accessing the site.
There is also an admin who manages the content. The admin can delete posts and comments, and ban users. Before banning, the admin sends a warning email to the user.

**Features**
Users can:
- Register and confirm their email address
- Log in securely
- Create, edit, and delete their own posts
- Add comments to posts, edit and delete them

The app includes security features to restrict access:
- Only users with verified emails can log in
- Banned users are prevented from logging in

Admin  can:
- Manage content by deleting any post or comment
- Ban users who violate rules
- Before banning, the admin sends a warning email to the user to notify them

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

**Running the Project**
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

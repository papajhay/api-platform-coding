# Bootstrap Symfony

This project is a Symfony 7.4 application with API Platform, Doctrine, Twig, JWT auth, and YAML-based API Platform resources.

## Actual project stack

- PHP 8.2+
- Symfony 7.4.*
- API Platform 4.3
- Doctrine ORM / Doctrine Bundle
- MakerBundle
- Twig
- LexikJWTAuthenticationBundle
- Gesdinet JWT Refresh Token Bundle
- VichUploaderBundle

## Bootstrap commands

These commands match the packages already present in `composer.json`:

```bash
composer create-project symfony/skeleton my-project
cd my-project
composer require api-platform/symfony api-platform/doctrine-orm doctrine/doctrine-bundle doctrine/orm symfony/twig-bundle twig/twig twig/extra-bundle
composer require lexik/jwt-authentication-bundle gesdinet/jwt-refresh-token-bundle
composer require vich/uploader-bundle nelmio/cors-bundle
composer require symfony/maker-bundle --dev
```

## Database URL

The project uses `DATABASE_URL` in `.env.local`:

```dotenv
DATABASE_URL="mysql://root:@127.0.0.1:3306/api-coding?serverVersion=mariadb-10.6.23&charset=utf8mb4"
```

If you are bootstrapping a fresh project, set the same env var before running Doctrine commands.

## Database creation

Typical Doctrine bootstrap commands:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## User entity

The project already uses a `User` entity with:

- `email`
- `roles`
- `password`
- `createdAt`
- `posts`
- `comments`

The entity is configured as the API user provider in `config/packages/security.yaml`.

## Security bootstrap

The login form routes are handled by `src/Controller/SecurityController.php`, and the main firewall redirects successful logins to `admin`.

The API uses JWT for stateless auth and `/api/login_check` for token acquisition.

## JWT key generation

Generate keys with Lexik:

```bash
php bin/console lexik:jwt:generate-keypair
```

The project stores keys in:

- `config/jwt/private.pem`
- `config/jwt/public.pem`

## Login check

The API login endpoint is configured at:

```yaml
json_login:
    check_path: /api/login_check
    username_path: email
    password_path: password
```

Use this exact path and field mapping when bootstrapping a compatible project.

## MakerBundle use

Use MakerBundle to generate the initial entity and controller skeletons, then convert the API layer to YAML resources:

```bash
php bin/console make:user
php bin/console make:entity
php bin/console make:controller
```

Keep the final API Platform configuration in YAML, not PHP attributes.

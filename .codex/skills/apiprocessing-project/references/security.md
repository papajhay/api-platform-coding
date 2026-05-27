# Security

This project uses Symfony security for both web and API access.

## Role hierarchy

```yaml
role_hierarchy:
    ROLE_USER: []
    ROLE_API_USER: [ROLE_USER]
    ROLE_ADMIN: [ROLE_USER]
    ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_API_USER]
```

## Firewalls

```yaml
firewalls:
    dev:
        pattern: ^/(_profiler|_wdt|assets|build)/
        security: false

    api:
        pattern: ^/api
        stateless: true
        entry_point: jwt
        provider: app_user_provider
        access_denied_handler: App\Security\ApiAccessDeniedHandler
        json_login:
            check_path: /api/login_check
            username_path: email
            password_path: password
        jwt: ~
        refresh_jwt:
            check_path: api_refresh_token

    main:
        lazy: true
        provider: app_user_provider
        form_login:
            login_path: app_login
            check_path: app_login
            enable_csrf: true
            default_target_path: admin
        logout:
            path: app_logout
            target: app_login
```

## Access control

```yaml
access_control:
    - { path: ^/admin, roles: ["ROLE_ADMIN", "ROLE_SUPER_ADMIN"]}
    - { path: ^/api/docs, roles: PUBLIC_ACCESS }
    - { path: ^/api$, roles: PUBLIC_ACCESS }
    - { path: ^/api/login_check, roles: PUBLIC_ACCESS}
    - { path: ^/api/token/refresh, roles: PUBLIC_ACCESS }
    - { path: ^/api/login, roles: PUBLIC_ACCESS }
    - { path: ^/api, roles: IS_AUTHENTICATED_FULLY }
```

## User provider

```yaml
providers:
    app_user_provider:
        entity:
            class: App\Entity\User
            property: email
```

## Security conventions

- Admin pages require `ROLE_ADMIN` or `ROLE_SUPER_ADMIN`.
- API reads require authenticated users.
- Mutating API calls use `ROLE_API_USER` or `ROLE_SUPER_ADMIN`.
- The web login redirects to `admin` after authentication.

## Important project-specific behavior

- `SecurityController::login()` redirects authenticated users to the admin dashboard.
- The API uses a dedicated access denied handler for API responses.
- `ROLE_API_USER` is distinct from `ROLE_ADMIN`, but `ROLE_SUPER_ADMIN` includes both admin and API capabilities.

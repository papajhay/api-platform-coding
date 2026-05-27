# Security

Admin access is controlled by Symfony security, not by ad hoc checks inside controllers.

## Role hierarchy

```yaml
role_hierarchy:
    ROLE_USER: []
    ROLE_API_USER: [ROLE_USER]
    ROLE_ADMIN: [ROLE_USER]
    ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_API_USER]
```

## Admin access control

```yaml
access_control:
    - { path: ^/admin, roles: ["ROLE_ADMIN", "ROLE_SUPER_ADMIN"]}
```

## Rules

- Use `ROLE_ADMIN` and `ROLE_SUPER_ADMIN` as the admin roles for new admin code.
- Do not invent new admin-only roles unless the project introduces them elsewhere.
- Preserve the current Symfony security model when generating admin controllers.

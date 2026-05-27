# Entities

## Current admin structure

There is one dashboard controller and three CRUD controllers:

- `DashboardController`
- `UserCrudController`
- `PostCrudController`
- `CommentCrudController`

All admin controllers live under `App\Controller\Admin`.

## What to inspect first

Before generating code, read the current project patterns:

- `src/Controller/Admin/DashboardController.php`
- `src/Controller/Admin/PostCrudController.php`
- `src/Controller/Admin/CommentCrudController.php`
- `src/Controller/Admin/UserCrudController.php`
- `config/packages/security.yaml`
- `config/routes/easyadmin.yaml`

## Routing note

This project does **not** use `config/packages/easy_admin.yaml`. The EasyAdmin routing is defined in `config/routes/easyadmin.yaml`.

```yaml
easyadmin:
    resource: .
    type: easyadmin.routes
```

## Rules

- Preserve the current controller names and namespace.
- Keep new CRUD controllers under `App\Controller\Admin`.
- Keep the existing admin route loader pattern.

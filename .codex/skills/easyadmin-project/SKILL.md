---
name: easyadmin-project
description: Generate and modify EasyAdmin code for this Symfony project by matching the existing admin controllers, route wiring, field choices, action patterns, and security model. Use when working under src/Controller/Admin/, config/packages/security.yaml, and config/routes/easyadmin.yaml so new admin code stays compatible with the project's conventions.
metadata:
  short-description: Symfony EasyAdmin patterns for this project
---

# EasyAdmin Project

Use this skill when creating or updating EasyAdmin code in this repository.

## What to inspect first

Before generating code, read the current project patterns:

- `src/Controller/Admin/DashboardController.php`
- `src/Controller/Admin/PostCrudController.php`
- `src/Controller/Admin/CommentCrudController.php`
- `src/Controller/Admin/UserCrudController.php`
- `config/packages/security.yaml`
- `config/routes/easyadmin.yaml`

Note: this project does **not** use `config/packages/easy_admin.yaml`. The EasyAdmin routing is defined in `config/routes/easyadmin.yaml`.

## Current admin structure

There is one dashboard controller and three CRUD controllers:

- `DashboardController`
- `UserCrudController`
- `PostCrudController`
- `CommentCrudController`

All admin controllers live under `App\Controller\Admin`.

## Project security pattern

Admin access is controlled by Symfony security, not by ad hoc checks inside controllers.

### Role hierarchy

From `config/packages/security.yaml`:

```yaml
role_hierarchy:
    ROLE_USER: []
    ROLE_API_USER: [ROLE_USER]
    ROLE_ADMIN: [ROLE_USER]
    ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_API_USER]
```

### Admin access control

```yaml
access_control:
    - { path: ^/admin, roles: ["ROLE_ADMIN", "ROLE_SUPER_ADMIN"]}
```

Use `ROLE_ADMIN` and `ROLE_SUPER_ADMIN` as the admin roles for new admin code. Do not invent new admin-only roles unless the project introduces them elsewhere.

## EasyAdmin routing pattern

The admin route is registered with the EasyAdmin route loader:

```yaml
easyadmin:
    resource: .
    type: easyadmin.routes
```

## Dashboard pattern

The dashboard controller uses the EasyAdmin dashboard attribute and returns a Twig template for the landing page.

```php
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
final class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Blog Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkTo(UserCrudController::class, 'Users', 'fa fa-users');
        yield MenuItem::linkTo(PostCrudController::class, 'Posts', 'fa fa-file-alt');
        yield MenuItem::linkTo(CommentCrudController::class, 'Comments', 'fa fa-comments');
    }
}
```

When adding new CRUD controllers, add them to `configureMenuItems()` with the same `MenuItem::linkTo(...)` style.

## CRUD controller pattern

Every CRUD controller in this project follows the same basic shape:

- `final class`
- `extends AbstractCrudController`
- `getEntityFqcn()`
- `configureCrud()`
- `configureActions()` when custom actions are needed
- `configureFields()`

### Common action pattern

Both `PostCrudController` and `CommentCrudController` add the same action set:

```php
public function configureActions(Actions $actions): Actions
{
    return $actions
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ->add(Crud::PAGE_EDIT, Action::INDEX)
        ->add(Crud::PAGE_EDIT, Action::DETAIL)
        ->add(Crud::PAGE_EDIT, Action::DELETE);
}
```

Reuse this pattern when creating new CRUD controllers that should behave like the existing post/comment admin screens.

## Field patterns from the project

Use the same field classes and visibility patterns already present in the repo.

### User admin fields

```php
public function configureFields(string $pageName): iterable
{
    yield IdField::new('id')->hideOnForm();
    yield EmailField::new('email');
    yield ArrayField::new('roles');
    yield TextField::new('password')->onlyOnForms();
}
```

Pattern notes:

- `IdField` is hidden on forms
- `EmailField` is used for the user email
- `ArrayField` is used for roles
- `TextField` is used for the password and only shown on forms

### Comment admin fields

```php
public function configureFields(string $pageName): iterable
{
    yield IdField::new('id')->hideOnForm();
    yield TextareaField::new('content');
    yield AssociationField::new('post');
    yield AssociationField::new('author');
}
```

### Post admin fields

```php
public function configureFields(string $pageName): iterable
{
    yield TextField::new('title');
    yield TextareaField::new('content');
    yield Field::new('imageFile')
        ->setFormType(VichImageType::class)
        ->setFormTypeOptions([
            'block_name' => 'dropzone_image',
        ])
        ->onlyOnForms();
    yield ImageField::new('imageName')
        ->setBasePath('/uploads/images/posts')
        ->onlyOnIndex();
    yield ImageField::new('imageName')
        ->setBasePath('/uploads/images/posts')
        ->onlyOnDetail();
    yield AssociationField::new('author');
}
```

Pattern notes:

- `TextField` is used for short text
- `TextareaField` is used for long text
- `AssociationField` is used for relations
- `Field::new('imageFile')` is paired with `VichImageType::class`
- `ImageField::new('imageName')` is used for display, with `/uploads/images/posts`
- `onlyOnForms()`, `onlyOnIndex()`, and `onlyOnDetail()` are used to split form vs display behavior

### Post-specific styling and assets

`PostCrudController` also customizes CRUD settings and assets:

```php
public function configureCrud(Crud $crud): Crud
{
    return $crud
        ->setEntityLabelInSingular('Post')
        ->setEntityLabelInPlural('Posts')
        ->setPaginatorPageSize(10)
        ->setDefaultSort(['createdAt' => 'DESC'])
        ->setFormThemes([
            'admin/form/dropzone_image_widget.html.twig',
            '@EasyAdmin/crud/form_theme.html.twig',
        ]);
}
```

```php
public function configureAssets(Assets $assets): Assets
{
    return $assets
        ->addCssFile(Asset::new('admin/dropzone-image.css')->onlyOnForms())
        ->addJsFile(Asset::new('admin/dropzone-image.js')->onlyOnForms());
}
```

## CRUD-specific CRUD settings

### User

```php
public function configureCrud(Crud $crud): Crud
{
    return $crud->setEntityLabelInSingular('User')->setEntityLabelInPlural('Users');
}
```

### Comment

```php
public function configureCrud(Crud $crud): Crud
{
    return $crud
        ->setEntityLabelInSingular('Comment')
        ->setEntityLabelInPlural('Comments')
        ->setPaginatorPageSize(10)
        ->setDefaultSort(['createdAt' => 'DESC']);
}
```

## Filters

There are no `configureFilters()` implementations in the current admin controllers.

If a task asks for filters, prefer to:

1. Match the same field vocabulary already used in the entity's CRUD controller.
2. Keep the filter list minimal and aligned with the existing admin screens.
3. Avoid introducing a filter pattern that the project does not already use unless the user explicitly wants a new convention.

## Generation rules

When Codex generates new EasyAdmin code for this project:

- Match the existing namespace, `declare(strict_types=1)`, and `final` class style.
- Prefer the same controller organization under `src/Controller/Admin/`.
- Use the same EasyAdmin field classes already used in nearby controllers.
- Keep labels, ordering, and paginator settings consistent with the entity family.
- Preserve the security model: `/admin` requires `ROLE_ADMIN` or `ROLE_SUPER_ADMIN`.
- If a new CRUD needs menu access, register it in `DashboardController::configureMenuItems()`.
- If a new entity has media uploads, follow the `PostCrudController` pattern with form-only upload fields and separate display fields.

## Good target output

A generated admin class is compatible when it:

- fits into the existing dashboard menu
- respects the current security rules
- uses the same field classes and visibility modifiers
- avoids introducing unreviewed EasyAdmin patterns that do not appear in this repo

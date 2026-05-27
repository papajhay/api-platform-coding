# CRUD

Every CRUD controller in this project follows the same basic shape:

- `final class`
- `extends AbstractCrudController`
- `getEntityFqcn()`
- `configureCrud()`
- `configureActions()` when custom actions are needed
- `configureFields()`

## Common action pattern

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

## CRUD-specific settings

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

### Post

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

### Post assets

```php
public function configureAssets(Assets $assets): Assets
{
    return $assets
        ->addCssFile(Asset::new('admin/dropzone-image.css')->onlyOnForms())
        ->addJsFile(Asset::new('admin/dropzone-image.js')->onlyOnForms());
}
```

## Filters

There are no `configureFilters()` implementations in the current admin controllers.

If a task asks for filters, prefer to:

1. Match the same field vocabulary already used in the entity's CRUD controller.
2. Keep the filter list minimal and aligned with the existing admin screens.
3. Avoid introducing a filter pattern that the project does not already use unless the user explicitly wants a new convention.

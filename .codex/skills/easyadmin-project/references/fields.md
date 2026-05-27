# Fields

Use the same field classes and visibility patterns already present in the repo.

## User fields

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

## Comment fields

```php
public function configureFields(string $pageName): iterable
{
    yield IdField::new('id')->hideOnForm();
    yield TextareaField::new('content');
    yield AssociationField::new('post');
    yield AssociationField::new('author');
}
```

## Post fields

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

# Dashboard

The dashboard controller uses the EasyAdmin dashboard attribute and returns a Twig template for the landing page.

## Dashboard controller

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

## Rules

- There is one dashboard controller in the current project.
- When adding new CRUD controllers, add them to `configureMenuItems()` with the same `MenuItem::linkTo(...)` style.
- Keep the dashboard title and menu wiring aligned with the existing controller.

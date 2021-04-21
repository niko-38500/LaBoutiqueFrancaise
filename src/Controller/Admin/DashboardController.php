<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use App\Entity\Category;
use App\Entity\HomeHeader;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(OrderCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('La boutique Francaise');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateur', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Commandes', 'fa fa-shopping-cart', Order::class);
        yield MenuItem::linkToCrud('Categories', 'fa fa-list', Category::class);
        yield MenuItem::linkToCrud('Produits', 'fa fa-tag', Product::class);
        yield MenuItem::linkToCrud('Transporteurs', 'fas fa-truck-moving', Carrier::class);
        yield MenuItem::linkToCrud('Headers', 'fa fa-desktop', HomeHeader::class);
    }
}

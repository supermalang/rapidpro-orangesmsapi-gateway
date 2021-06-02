<?php

namespace App\Controller\Admin;

use App\Entity\Channel;
use App\Entity\DeliveryNotifications;
use App\Entity\Message;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Apismsgateway')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Messages', 'fas fa-list', Message::class);
        yield MenuItem::linkToCrud('DR Notifications', 'fas fa-list', DeliveryNotifications::class);
        yield MenuItem::linkToCrud('Channels', 'fas fa-list', Channel::class);
    }
}

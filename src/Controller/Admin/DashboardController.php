<?php

namespace App\Controller\Admin;

use App\Entity\Channel;
use App\Entity\DeliveryNotifications;
use App\Entity\Message;
use App\Entity\Token;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
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
        // you can also render some template to display a proper Dashboard
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        return $this->render('Admin/dashboard-index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Orange SMS API Gateway')
        ;
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('https://cdn.tailwindcss.com')
            //->addJsFile('https://example.org/js/admin2.js')
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Messages', 'fa fa-comments-o', Message::class);
        yield MenuItem::linkToCrud('DR Notifications', 'fa fa-bell', DeliveryNotifications::class);
        yield MenuItem::linkToCrud('Channels', 'fas fa-rocket', Channel::class);
        yield MenuItem::linkToCrud('Tokens', 'fa fa-shield', Token::class);
    }
}

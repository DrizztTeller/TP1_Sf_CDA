<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\Event;
use App\Entity\Speaker;
use Doctrine\ORM\EntityManager;
use App\Repository\PostRepository;
use App\Repository\EventRepository;
use App\Repository\SpeakerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    // Declaratiton of repositories
    private EventRepository $eventRepository;
    private SpeakerRepository $speakerRepository;
    private PostRepository $postRepository;

    // Constructor
    public function __construct(
        EventRepository $eventRepository,
        SpeakerRepository $speakerRepository,
        PostRepository $postRepository
    ) {
        $this->eventRepository = $eventRepository;
        $this->speakerRepository = $speakerRepository;
        $this->postRepository = $postRepository;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $events = $this->eventRepository->findAll(); // All events
        $speakers = $this->speakerRepository->findAll(); // All speakers
        $pastEvents = [];
        $posts = $this->postRepository->findAll(); // All posts
        foreach ($events as $evt) {
            if ($evt->getDate() < new \DateTime()) {
                array_push($pastEvents, $evt);
            }
        } // Past events
        return $this->render('admin/dashboard.html.twig', [
            'events' => $events,
            'speakers' => $speakers,
            'pastEvents' => $pastEvents,
            'posts' => $posts,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Supatalks');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Events', 'fas fa-list', Event::class);
        yield MenuItem::linkToCrud('Speakers', 'fas fa-bullhorn', Speaker::class);
        yield MenuItem::linkToCrud('Posts', 'fas fa-message', Post::class);
        yield MenuItem::linkToRoute('Back to website', 'fa fa-arrow-left', 'app_home');

        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
} // Do not write anything after this line

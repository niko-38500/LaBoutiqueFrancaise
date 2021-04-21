<?php

namespace App\Controller;

use App\Classes\MailerHandler;
use App\Entity\HomeHeader;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $bestProducts = $this->entityManager->getRepository(Product::class)->findByIsBest(1);
        $headers = $this->entityManager->getRepository(HomeHeader::class)->findAll();

        return $this->render('home/index.html.twig', [
            'bestProducts' => $bestProducts,
            'headers' => $headers
        ]);
    }
}

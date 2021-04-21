<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/compte", name="user_")
 */

class UserOrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }
    
    /**
     * @Route("/commandes", name="order")
     */
    public function index()
    {
        $user = $this->getUser();

        $order = $this->entityManager->getRepository(Order::class)->findSuccessOrders($user);

        return $this->render('user/orders.html.twig', [
            'orders' => $order
        ]);
    }
    
    /**
     * @Route("/commande/{reference}", name="order_show")
     */
    public function show($reference)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);
        

        return $this->render('user/showOrder.html.twig', [
            'order' => $order
        ]);
    }

}
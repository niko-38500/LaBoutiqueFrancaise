<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\MailerHandler;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class StripeStatementController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }

    /**
     * @Route("/commande/success/{stripeSessionId}", name="order_valid_payement")
     */
    public function success($stripeSessionId, SessionInterface $session, Cart $cart, MailerInterface $mailer)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($order->getState() == 0) {
            $session->remove('carrier');
            $cart->remove();

            $orderDetails = $order->getOrderDetails();
            $options = [
                'username' => $this->getUser()->getFullName(),
                'reference' => $order->getReference(),
                'order_details' => $orderDetails
            ];

    
            $mail = new MailerHandler;
            $mail->sendWithTemplate($mailer, $order->getUser()->getEmail(), 'Commande n°'. $order->getReference() .' paiement validé', 'email_template/order_payed.html.twig', $options);  

            $order->setState(1);

            $this->entityManager->flush();  
        }


        return $this->render('stripe/stripe_statement/success.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/commande/error/{stripeSessionId}", name="order_error_payement")
     */
    public function error($stripeSessionId)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('stripe/stripe_statement/error.html.twig', [
            'order' => $order,
        ]);
    }
}

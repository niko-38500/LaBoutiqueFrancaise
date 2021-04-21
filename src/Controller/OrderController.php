<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Carrier;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }

    /**
     * @Route("/commande", name="order")
     */
    public function index(SessionInterface $session, Cart $cart)
    {
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('user_add_address');
        }

        if (isset($_POST['carrierId'])) {
            $carrierId = $_POST['carrierId'];
            $session->set('carrier', $carrierId);
        } else if (!$session->get('carrier')) {
            return $this->redirectToRoute('cart');
        }

        $carrier = $this->getDoctrine()->getRepository(Carrier::class)->findOneById($session->get('carrier'));

        $session->set('carrier', $carrier);

        $form = $this->createForm(OrderType::class, null, [
            'action' => $this->generateUrl('add_order'),
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'carrier' =>  $carrier,
            'form'    => $form->createView(),
            'cart'    => $cart->getCart()
        ]);
    }
    
    /**
     * @Route("/commande/add", name="add_order")
     */
    public function add(SessionInterface $session, Cart $cart, Request $request)
    {
        $carrier = $session->get('carrier');

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $carrier = $session->get('carrier');
            $delivery = $form->get('addresses')->getData();
            $deliveryContent = $delivery->getFirstname() . ' ' . $delivery->getLastname().'<br/>'.$delivery->getPhone();

            if ($delivery->getCompany()) {
                $deliveryContent .= '<br/>'.$delivery->getCompany();
            }

            $deliveryContent .= '<br/>'.$delivery->getAddress().'<br/>'.$delivery->getPostal().' - '.$delivery->getCity().'<br/>'.$delivery->getCountry();

            $date = new DateTime();
            $reference = $date->format('dmY') . '-' . uniqid();

            $order = new Order;
            $order
                ->setReference($reference)
                ->setUser($this->getUser())
                ->setCreatedAt($date)
                ->setCarrierName($carrier->getName())
                ->setCarrierPrice($carrier->getPrice())
                ->setDelivery($deliveryContent)
                ->setState(0)
            ;

            $this->entityManager->persist($order);

            foreach ($cart->getCart() as $product) {
                $orderDetails = new OrderDetails;

                $orderDetails
                    ->setMyOrder($order)
                    ->setProduct($product['product']->getName())
                    ->setQuantity($product['quantity'])
                    ->setPrice($product['product']->getPrice())
                    ->setTotal($product['product']->getPrice() * $product['quantity'])
                    ->setIllustration($product['product']->getIllustration())
                ;

                $this->entityManager->persist($orderDetails);
            }

            $this->entityManager->flush();
        }

        return $this->render('order/add.html.twig', [
            'reference' => $order->getReference(),
            'carrier' => $carrier,
            'cart' => $cart->getCart(),
            'address' => $order->getDelivery()
        ]);
    }
}

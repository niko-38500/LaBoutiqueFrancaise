<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Carrier;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }

    /**
     * @Route("/panier", name="cart")
     */
    public function index(Cart $cart): Response
    {
        if ($this->getUser()) {
            $carrier = $this->getDoctrine()->getRepository(Carrier::class)->findAll();

            return $this->render('cart/index.html.twig', [
                'cart' => $cart->getCart(),
                'carrier' => $carrier,
            ]);
        } else {
            $this->addFlash('errorAuthentication', 'Pour accéder à cette partie merci de vous connecter ou de crée un compte');
            return $this->redirectToRoute('home');
        }
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     */
    public function add($id, Cart $cart): RedirectResponse
    {
    // a implémenter : box de confirmation pour aller au panier ou continué ses achat
        $cart->add($id);
    
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/remove", name="remove_my_cart")
     */
    public function remove(Cart $cart): Response
    {
        $cart->remove();

        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/cart/remove-one-item/{id}", name="remove_item_cart")
     */
    public function removeOne(Cart $cart, $id): Response
    {
        $cart->removeOne($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/remove-one-quantity/{id}", name="decrease_item_cart")
     */
    public function removeOneQuantity(Cart $cart, $id): Response
    {
        $cart->removeOneQuantity($id);

        return $this->redirectToRoute('cart');
    }
}

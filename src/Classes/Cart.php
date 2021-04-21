<?php

namespace App\Classes;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{

    private $session;
    private $entityManager;

    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->entityManager = $em;
    }

    /**
     * Add an item to the cart
     */
    public function add($id)
    {
        $productObject = $this->entityManager->getRepository(Product::class)->findOneById($id);

        if ($productObject) {
            $cart = $this->session->get('cart', []);

            if (!empty($cart[$id])) {
                $cart[$id]++;
            } else {
                $cart[$id] = 1;
            }

            $this->session->set('cart', $cart);
        }
    }

    /**
     * get the products on your session storage cart and convert there into a database object
     */
    public function getCart()
    {
        $cartComplete = [];

        if ($this->get()) {
            foreach ($this->get() as $id => $quantity) {
                $cartComplete[] = [
                    'product' => $this->entityManager->getRepository(Product::class)->findOneById($id),
                    'quantity' => $quantity
                ];
            }
        }

        return $cartComplete;
    }

    /**
     * get the products into your cart from the session storage
     */
    public function get()
    {
        return $this->session->get('cart');
    }

    /**
     * delete all of your cart
     */
    public function remove()
    {
        return $this->session->remove('cart');
    }

    /**
     * remove one item from the cart
     */
    public function removeOne($id)
    {
        $cart = $this->session->get('cart', []);

        unset($cart[$id]);

        return $this->session->set('cart', $cart);
    }

    /**
     * remove one quantity of an item on the cart
     */
    public function removeOneQuantity($id)
    {
        $cart = $this->session->get('cart', []);

        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }

        return $this->session->set('cart', $cart);
    }
}
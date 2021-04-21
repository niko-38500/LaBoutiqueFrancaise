<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/compte", name="user_")
 */

class UserAddressController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }

    /**
     * @Route("/address", name="address")
     */
    public function indexAddress() 
    {
        return $this->render("user/address.html.twig");
    }

    /**
     * @Route("/address/add", name="add_address")
     */
    public function addAddress(Request $request, Cart $cart) 
    {
        $address = new Address();

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());

            $this->entityManager->persist($address);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre addresse à bien été ajouté');

            if ($cart->get()) {
                return $this->redirectToRoute('order');
            }

            return $this->redirectToRoute('user_address');
        }

        return $this->render("user/formAddress.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/address/edit/{id}", name="edit_address")
     */
    public function editAddress(Request $request, $id)
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if (!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('user_address');
        }

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre addresse à bien été mis à jour');
            return $this->redirectToRoute('user_address');
        }

        return $this->render("user/formAddress.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/address/delete/{id}", name="delete_address")
     */
    public function deleteAddress($id)
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if ($address && $address->getUser() == $this->getUser()) {
            $this->entityManager->remove($address);
            $this->entityManager->flush();
        }

        $this->addFlash('success', 'Votre adresse à bien été supprimé');
        return $this->redirectToRoute('user_address');
    }
}
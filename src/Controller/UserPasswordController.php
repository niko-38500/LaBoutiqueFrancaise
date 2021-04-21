<?php

namespace App\Controller;

use App\Form\UpdatePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/compte", name="user_")
 */

class UserPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }

    /**
     * @Route("/update-password", name="updatePassword")
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = $this->getUser();

        $form = $this->createForm(UpdatePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();

            if ($encoder->isPasswordValid($user, $oldPassword)) {
                $newPassword = $form->get('newPassword')->getData();
                $password = $encoder->encodePassword($user, $newPassword);
                
                $user->setPassword($password);
                
                $this->entityManager->flush();

                $this->addFlash('successPasswordReset', 'Votre mot de passe a bien été mis à jour');
                return $this->redirectToRoute('user_account');
            } else {
                $this->addFlash("error", "Votre mot de passe actuel est incorrect");
            }
        }

        return $this->render('user/updatePassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }



}
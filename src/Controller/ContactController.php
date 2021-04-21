<?php

namespace App\Controller;

use App\Classes\MailerHandler;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $mail = new MailerHandler();
            $mail->receiveContactEmail($mailer, htmlspecialchars($data['email']), htmlspecialchars($data['subject']), htmlspecialchars($data['content']));
            $this->addFlash('success', 'Votre demande de contact à bien été prise en compte notre equipe vas vous repondre dans les plus bref delais');
            return $this->redirectToRoute('home');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

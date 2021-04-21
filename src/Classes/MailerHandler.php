<?php

namespace App\Classes;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailerHandler
{
    public function send(MailerInterface $mailer, $to, $subject, $message)
    {
        $email = (new Email())
            ->from('contact@laboutiquefrancaise.fr')
            ->to($to)
            ->subject($subject)
            ->html($message);

        $mailer->send($email);
    }

    public function sendWithTemplate(MailerInterface $mailer, $to, $subject, $templatePath, array $options)
    {
        $email = (new TemplatedEmail())
            ->from('contact@laboutiquefrancaise.fr')
            ->to(new Address($to))
            ->subject($subject)

            // path of the Twig template to render
            ->htmlTemplate($templatePath)

            // pass variables (name => value) to the template
            ->context($options)
        ;

        $mailer->send($email);
    }

    public function receiveContactEmail(MailerInterface $mailer, $from, $subject, $message)
    {
        $email = (new Email())
            ->from($from)
            ->to('admin@laboutiquefrancaise.fr')
            ->subject($subject)
            ->text($message);

        $mailer->send($email);
    }
}
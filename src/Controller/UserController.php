<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/compte", name="user_")
 */

class UserController extends AbstractController
{
    /**
     * @Route("/", name="account")
     */
    public function index()
    {
        return $this->render('user/index.html.twig');
    }
}

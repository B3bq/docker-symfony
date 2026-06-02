<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SignController extends AbstractController
{
    #[Route('/signin', name: 'signin')]
    public function signin(): Response
    {
        return $this->render('content/signin.html.twig');
    }
}
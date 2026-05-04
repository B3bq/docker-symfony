<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainPageController extends AbstractController
{
    
    public function index(): Response
    {
        return $this->render('main_page.html.twig');
    }
}
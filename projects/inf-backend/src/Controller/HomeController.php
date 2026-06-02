<?php

namespace App\Controller;

USE App\Repository\SurveyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage', methods: ['POST', 'GET'])]
    public function index(SurveyRepository $surveyRepository): Response
    {
        $user = true;

        // Pobieramy wszystkie ankiety z bazy danych
        $surveys = $surveyRepository->findAll();

        return $this->render('content/main_page.html.twig', [
            'user' => $user,
            'content' => $surveys,
        ]);
    }

    #[Route('/signup', name: 'signup')]
    public function signup(): Response
    {
        return $this->render('content/signup.html.twig');
    }

}
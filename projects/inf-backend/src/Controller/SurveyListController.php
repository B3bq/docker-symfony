<?php

namespace App\Controller;

use App\Entity\Survey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SurveyListController extends AbstractController
{
    #[Route('/survey/list', name: 'survey_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $userId = $this->getUser()->getId();
        $surveys = $em->getRepository(Survey::class)->findBy(['user_id' => $userId]);

        return $this->render('content/survey_list.html.twig', [
            'surveys' => $surveys,
        ]);
    }
}
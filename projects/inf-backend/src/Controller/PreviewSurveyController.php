<?php

namespace App\Controller;

use App\Entity\Survey;
use App\Form\Type\SurveyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PreviewSurveyController extends AbstractController
{
    #[Route('/survey/{id}/preview', name: 'survey_preview')]
    public function preview(Survey $survey, Request $request, EntityManagerInterface $em): Response
    {
        // Sprawdzenie, czy aktualny użytkownik jest właścicielem ankiety
        if ($survey->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Nie masz uprawnień do podglądu tej ankiety.');
        }

        $form = $this->createForm(SurveyType::class, $survey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('content/preview_survey.html.twig', [
            'form' => $form->createView(),
            'survey' => $survey,
        ]);
    }
}
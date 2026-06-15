<?php

namespace App\Controller;

use App\Entity\Survey;
use App\Form\Type\SurveyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SurveyEditController extends AbstractController
{
    #[Route('/survey/{id}/edit', name: 'survey_edit')]
    public function edit(Request $request, EntityManagerInterface $em, Survey $survey): Response
    {
        // Sprawdzenie, czy aktualny użytkownik jest właścicielem ankiety
        if ($survey->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Nie masz uprawnień do edycji tej ankiety.');
        }

        $form = $this->createForm(SurveyType::class, $survey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('content/survey_edit.html.twig', [
            'form' => $form->createView(),
            'survey' => $survey,
        ]);
    }

    #[Route('/survey/{id}/delete', name: 'survey_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $em, Survey $survey): Response
    {
        // Only owner can delete
        if ($survey->getUserId() !== $this->getUser()->getId()) {
            throw $this->createAccessDeniedException('Nie masz uprawnień do usunięcia tej ankiety.');
        }

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete'.$survey->getId(), $token)) {
            throw $this->createAccessDeniedException('Niepoprawny token CSRF.');
        }

        $em->remove($survey);
        $em->flush();

        return $this->redirectToRoute('homepage');
    }
}
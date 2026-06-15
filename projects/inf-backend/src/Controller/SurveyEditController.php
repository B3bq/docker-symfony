<?php

namespace App\Controller;

class SurveyEditController extends SurveyCreatorController
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

        return $this->render('content/survey_create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
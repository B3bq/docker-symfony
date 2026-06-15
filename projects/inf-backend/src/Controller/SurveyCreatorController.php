<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Survey;
use App\Form\Type\SurveyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SurveyCreatorController extends AbstractController
{
    #[Route('/survey/create', name: 'survey_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $survey = new Survey();
        $survey->setUserId($this->getUser()->getId()); // ustawienie id użytkownika tworzącego ankietę

        // Inicjalizacja 3 pytań po 3 warianty odpowiedzi (tylko na starcie)
        if (!$request->isMethod('POST')) {
            for ($i = 0; $i < 3; $i++) {
                $q = new Question();
                for ($j = 0; $j < 3; $j++) {
                    $a = new Answer();
                    if ($j === 0) $a->setIsCorrect(true); // Domyślnie pierwsza odpowiedź poprawna
                    $q->addAnswer($a);
                }
                $survey->addQuestion($q);
            }
        }

        $form = $this->createForm(SurveyType::class, $survey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($survey);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('content/survey_create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

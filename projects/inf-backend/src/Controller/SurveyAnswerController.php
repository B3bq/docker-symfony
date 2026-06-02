<?php

namespace App\Controller;

use App\Entity\Survey;
use App\Repository\AnswerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SurveyAnswerController extends AbstractController
{
    #[Route('/survey/answer/{id}', name: 'survey_answer')]
    public function index(Survey $survey, Request $request, AnswerRepository $answerRepo): Response
    {
        $score = null;
        $maxScore = count($survey->getQuestions());

        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            
            $score = 0;
            foreach ($survey->getQuestions() as $question) {
                $fieldName = 'question_' . $question->getId();
                if (isset($data[$fieldName])) {
                    $selectedAnswerId = $data[$fieldName];
                    $selectedAnswer = $answerRepo->find($selectedAnswerId);
                    
                    if ($selectedAnswer && $selectedAnswer->isCorrect()) {
                        $score++;
                    }
                }
            }
        }

        return $this->render('content/survey_answer.html.twig', [
            'survey' => $survey,
            'score' => $score,
            'maxScore' => $maxScore
        ]);
    }
}
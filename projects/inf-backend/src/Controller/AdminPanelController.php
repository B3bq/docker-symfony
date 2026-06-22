<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Survey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPanelController extends AbstractController
{
    #[Route('/adminpanel', name: 'admin_panel')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        $availableRoles = [
            'ROLE_VIEWER',
            'ROLE_USER',
            'ROLE_ADMIN'
        ];

        return $this->render('content/admin_panel.html.twig', [
            'users' => $users,
            'available_roles' => $availableRoles,
        ]);
    }

    #[Route('/adminpanel/user-surveys/{id}', name: 'admin_user_surveys', methods: ['GET'])]
    public function userSurveys(int $id, EntityManagerInterface $entityManager): Response
    {
        $surveys = $entityManager->getRepository(Survey::class)->findBy(['user_id' => $id]);

        $data = array_map(function ($s) {
            return [
                'id' => $s->getId(),
                'title' => $s->getTitle(),
                'description' => $s->getDescription(),
            ];
        }, $surveys);

        return $this->json($data);
    }

    #[Route('/adminpanel/delete/{id}', name: 'admin_delete_user', methods: ['GET', 'POST'])]
    public function deleteUser(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->render('content/not_found.html.twig', [
                'message' => 'Nie znaleziono użytkownika o podanym ID.'
            ]);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Użytkownik został usunięty'
        );

        return $this->redirectToRoute('admin_panel');
    }

    #[Route('/adminpanel/delete-survey/{id}', name: 'admin_delete_survey', methods: ['POST'])]
    public function deleteSurvey(int $id, EntityManagerInterface $entityManager): Response
    {
        $survey = $entityManager->getRepository(Survey::class)->find($id);

        if (!$survey) {
            return $this->render('content/not_found.html.twig', [
                'message' => 'Nie znaleziono ankiety o podanym ID.'
            ]);
        }

        $entityManager->remove($survey);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Ankieta została usunięta'
        );

        return $this->redirectToRoute('admin_panel');
    }

    #[Route('/adminpanel/update-roles/{id}', name: 'admin_update_user_roles', methods: ['POST'])]
    public function updateUserRoles(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['error' => 'Użytkownik nie istnieje.'], 404);
        }

        $content = $request->getContent();
        $data = [];

        if ($content) {
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data = $decoded;
            }
        }

        if (empty($data)) {
            $data = $request->request->all();
        }

        $roles = [];
        if (isset($data['roles']) && is_array($data['roles'])) {
            $roles = $data['roles'];
        }

        // pewnosc, ze zawsze jest prezentowane ROLE_UESR
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }

        $user->setRoles(array_values(array_unique($roles)));
        $entityManager->persist($user);
        $entityManager->flush();
        
        $this->addFlash(
            'success',
            'Twoje zmiany zostały zapisane'
        );

        // zbieranie flasha do jsona
        $flashes = [];
        $session = $request->getSession();
        if ($session) {
            $flashBag = $session->getFlashBag()->all();
            $flashes = $flashBag;
        }

        return $this->json(['success' => true, 'roles' => $user->getRoles(), 'flashes' => $flashes]);
    }
}
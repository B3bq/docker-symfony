<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Survey;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPanelController extends AbstractController
{
    #[Route('/adminpanel', name: 'admin_panel')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('content/admin_panel.html.twig', [
            'users' => $users,
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

        return $this->redirectToRoute('admin_panel');
    }

    #[Route('/adminpanel/edit/{id}', name: 'admin_edit_user', methods: ['GET', 'POST'])]
    public function editUser(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->render('content/not_found.html.twig', [
                'message' => 'Nie znaleziono użytkownika o podanym ID.'
            ]);
        }

        return $this->render('content/edit_user.html.twig', [
            'user' => $user,
        ]);
    }
}
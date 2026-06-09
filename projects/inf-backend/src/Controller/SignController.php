<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SignController extends AbstractController
{
    #[Route('/signin', name: 'signin')]
    public function signin(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(\App\Form\LoginFormType::class);

        // Odbieranie błędów z systemu Security
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $form->addError(new \Symfony\Component\Form\FormError(
                $error->getMessageKey()
            ));
        }

        // Zapisywanie ostatniego emaila w polu email w formularzu
        $lastUsername = $authenticationUtils->getLastUsername();
        if ($lastUsername) {
            $form->get('email')->setData($lastUsername);
        }

        return $this->render('content/signin.html.twig', [
            'loginForm' => $form->createView(),
        ]);
    }
}
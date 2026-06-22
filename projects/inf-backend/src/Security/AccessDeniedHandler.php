<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function __construct(
        private Environment $twig
    ) {}

    public function handle($request, AccessDeniedException $accessDeniedException): ?Response
    {
        return new Response(
            $this->twig->render('content/access_denied.html.twig', [
                'message' => 'Nie masz uprawnień do tej strony.'
            ]),
            403
        );
    }
}
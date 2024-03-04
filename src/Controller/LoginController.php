<?php

namespace App\Controller;

use App\Services\ApiClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    public function __construct(
        private ApiClient $apiClient
    ){
    }

    #[Route('/login', name: 'login_page', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('login/index.html.twig');
    }

    #[Route('/login', name: 'login', methods: 'POST')]
    public function login(Request $request): Response
    {
        $email = $request->request->get('_username');
        $password = $request->request->get('_password');

        $apiResponse = $this->apiClient->authenticate($email, $password);

        if ($apiResponse !== null && isset($apiResponse['token_key'])) {
            $request->getSession()->set('access_token', $apiResponse['token_key']);
            $request->getSession()->set(
                'username',
                $apiResponse['user']['first_name'] . ' ' . $apiResponse['user']['last_name']
            );

            $this->addFlash('success', 'Login successful!');

            return $this->redirectToRoute('authors_list');
        }

        if (!empty($request->getSession()->get('access_token'))) {
            return $this->redirectToRoute('authors_list');
        }

        $this->addFlash('error', 'Invalid email or password.');

        return $this->redirectToRoute('login_page');
    }

    #[Route('/logout', name: 'logout')]
    public function logout(Request $request): Response
    {
        $request->getSession()->clear();

        return $this->render('login/index.html.twig');
    }
}

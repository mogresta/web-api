<?php

namespace App\Controller;

use App\Services\ApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthorController extends AbstractController
{
    public function __construct(
        private ApiClient $apiClient,
    ){
    }

    #[Route('/authors', name: 'authors_list', methods: 'GET')]
    public function index(): Response
    {
        $authors = $this->apiClient->listAuthors();

        return $this->render('author/index.html.twig', [
            'authors' => $authors['items'],
        ]);
    }

    #[Route('/author/{id}', name: 'author_view', methods: 'GET')]
    public function viewAuthor($id)
    {
        $response = $this->apiClient->viewAuthor($id);

        return $this->render('author/view.html.twig', [
            'author' => $response,
        ]);
    }

    #[Route('/author', name: 'author_new', methods: 'GET')]
    public function createAuthor()
    {
        return $this->render('author/create.html.twig');
    }

    #[Route('/author', name: 'author_create', methods: 'POST')]
    public function createNewAuthor(Request $request)
    {
        $data = $request->request->all();

        $this->apiClient->createAuthor($data);

        return $this->redirectToRoute('authors_list');
    }

    #[Route('/author/{id}/delete', name: 'author_delete', methods: 'GET')]
    public function deleteAuthor($id)
    {
        $this->apiClient->deleteAuthor($id);

        return $this->redirectToRoute('authors_list');
    }
}

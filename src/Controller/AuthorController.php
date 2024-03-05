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
    public function viewAuthor($id): Response
    {
        $response = $this->apiClient->viewAuthor($id);

        return $this->render('author/view.html.twig', [
            'author' => $response,
        ]);
    }

    #[Route('/author', name: 'author_new', methods: 'GET')]
    public function createAuthor(): Response
    {
        return $this->render('author/create.html.twig');
    }

    #[Route('/author', name: 'author_create', methods: 'POST')]
    public function createNewAuthor(Request $request): Response
    {
        $data = $request->request->all();

        $response = $this->apiClient->createAuthor($data);

        $request->getSession()->getFlashBag()->clear();

        if ($response->getStatusCode() === 200) {
            $this->addFlash('success', 'Author successfully created!');
        }

        if ($response->getStatusCode() === 500) {
            $this->addFlash('error', 'Something went wrong!');
        }

        return $this->redirectToRoute('authors_list');
    }

    #[Route('/author/{id}/delete', name: 'author_delete', methods: 'GET')]
    public function deleteAuthor($id, Request $request): Response
    {
        $response = $this->apiClient->deleteAuthor($id);

        $request->getSession()->getFlashBag()->clear();

        if ($response->getStatusCode() === 204) {
            $this->addFlash('success', 'Author successfully deleted!');
        }

        if ($response->getStatusCode() === 500) {
            $this->addFlash('error', 'Something went wrong!');
        }

        return $this->redirectToRoute('authors_list');
    }
}

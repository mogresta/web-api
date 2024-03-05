<?php

namespace App\Controller;

use App\Services\ApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class BookController extends AbstractController
{
    public function __construct(
        private ApiClient $apiClient,
    ){
    }

    #[Route('/book', name: 'book', methods: 'GET')]
    public function index(): Response
    {
        $authors = $this->apiClient->listAuthors();

        return $this->render('book/index.html.twig', [
            'authors' => $authors['items'],
        ]);
    }

    #[Route('/book/create', name: 'book_create', methods: 'POST')]
    public function createBook(Request $request): Response
    {
        $data = $request->request->all();

        $response = $this->apiClient->createBook($data);

        $request->getSession()->getFlashBag()->clear();

        if ($response->getStatusCode() === 200) {
            $this->addFlash('success', 'Book successfully created!');
        }

        if ($response->getStatusCode() === 500) {
            $this->addFlash('error', 'Something went wrong!');
        }

        return $this->redirectToRoute('book');
    }

    #[Route('/book/{id}/delete', name: 'book_delete', methods: 'GET')]
    public function deleteBook($id, Request $request): Response
    {
        $response = $this->apiClient->deleteBook($id);

        $request->getSession()->getFlashBag()->clear();

        if ($response->getStatusCode() === 204) {
            $this->addFlash('success', 'Book successfully deleted!');
        }

        if ($response->getStatusCode() === 500) {
            $this->addFlash('error', 'Something went wrong!');
        }

        return $this->redirect($request->getUri());
    }
}

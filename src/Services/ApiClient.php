<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\HttpFoundation\RequestStack;
use GuzzleHttp\Psr7\Response;

class ApiClient
{
    private GuzzleClient $guzzleClient;
    private string $token;

    public function __construct(
        private RequestStack $requestStack,
        string $baseUri
    ){
        $this->guzzleClient = new GuzzleClient([
            'base_uri' => $baseUri,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    public function authenticate(string $email, string $password): array
    {
        if (empty($this->token)) {
            $response = $this->guzzleClient->request( 'POST', '/api/v2/token',
                [ 'json' =>
                    [
                        'email' => $email,
                        'password' => $password,
                    ]
                ]
            );

            return json_decode($response->getBody()->getContents(), true);
        }

        return [];
    }

    public function listAuthors(): array
    {
        $this->token = $this->requestStack->getSession()->get('access_token');

        if (!empty($this->token)) {
            $response = $this->guzzleClient->request('GET', '/api/v2/authors',
                ['headers' => [ 'Authorization' => "Bearer {$this->token}" ]]
            );

            return json_decode($response->getBody()->getContents(), true);
        }

        return [];
    }

    public function deleteBook(int $id): Response
    {
        $this->token = $this->requestStack->getSession()->get('access_token');

        if (!empty($this->token)) {
            return $this->guzzleClient->request('DELETE', "/api/v2/books/{$id}",
                [
                    'headers' => [ 'Authorization' => "Bearer {$this->token}" ],
                ]
            );
        }

        return new Response(500);
    }

    public function deleteAuthor(int $id): Response
    {
        $this->token = $this->requestStack->getSession()->get('access_token');

        if (!empty($this->token)) {
            return $this->guzzleClient->request('DELETE', "/api/v2/authors/{$id}",
                [
                    'headers' => [ 'Authorization' => "Bearer {$this->token}" ],
                ]
            );
        }

        return new Response(500);
    }

    public function viewAuthor(int $id): array
    {
        $this->token = $this->requestStack->getSession()->get('access_token');

        if (!empty($this->token)) {
            $response = $this->guzzleClient->request('GET', "/api/v2/authors/{$id}",
                [
                    'headers' => [ 'Authorization' => "Bearer {$this->token}" ],
                ]
            );

            return json_decode($response->getBody()->getContents(), true);
        }

        return [];
    }

    public function createBook(array $data): Response
    {
        $this->token = $this->requestStack->getSession()->get('access_token');

        if (!empty($this->token)) {
            return $this->guzzleClient->request('POST', "/api/v2/books",
                [
                    'headers' => [
                        'Authorization' => "Bearer {$this->token}"
                    ],
                    'json' => [
                        'author' => [
                            'id' => $data['author']
                        ],
                        'title' => $data['title'],
                        'release_date' => $data['release_date'],
                        'description' => $data['description'],
                        'isbn' => $data['isbn'],
                        'format' => $data['format'],
                        'number_of_pages' => (int) $data['pages'],
                    ],
                ]
            );
        }

        return new Response(500);
    }

    public function createAuthor(array $data): Response
    {
        $this->token = $this->requestStack->getSession()->get('access_token');

        if (!empty($this->token)) {
            return $this->guzzleClient->request('POST', "/api/v2/authors",
                [
                    'headers' => [
                        'Authorization' => "Bearer {$this->token}"
                    ],
                    'json' => [
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'birthday' => $data['birthday'],
                        'biography' => $data['biography'],
                        'gender' => $data['gender'],
                        'place_of_birth' => $data['place_of_birth'],
                    ],
                ]
            );
        }

        return new Response(500);
    }
}
<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\HttpFoundation\RequestStack;

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

    public function deleteBook(int $id): void
    {
        $this->token = $this->requestStack->getSession()->get('access_token');

        if (!empty($this->token)) {
            $this->guzzleClient->request('DELETE', "/api/v2/books/{$id}",
                [
                    'headers' => [ 'Authorization' => "Bearer {$this->token}" ],
                ]
            );
        }
    }

    public function createBook(array $data): array
    {
        $this->token = $this->requestStack->getSession()->get('access_token');

        if (!empty($this->token)) {
            $response = $this->guzzleClient->request('POST', "/api/v2/books",
                [
                    'headers' => [ 'Authorization' => "Bearer {$this->token}" ],
                    'json' => [
                        'title' => $data['title'],
                        'release_date' => $data['release_date'],
                        'description' => $data['description'],
                        'isbn' => $data['isbn'],
                        'format' => $data['format'],
                        'pages' => (int) $data['pages'],
                        'author' => (int) $data['author'],
                    ],
                ]
            );

            return json_decode($response->getBody()->getContents(), true);
        }

        return [];
    }

    public function deleteAuthor(int $id): void
    {
        $this->token = $this->requestStack->getSession()->get('access_token');

        if (!empty($this->token)) {
            $this->guzzleClient->request('DELETE', "/api/v2/authors/{$id}",
                [
                    'headers' => [ 'Authorization' => "Bearer {$this->token}" ],
                ]
            );
        }
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

    public function createAuthor(array $data): array
    {
        $this->token = $this->requestStack->getSession()->get('access_token');

        if (!empty($this->token)) {
            $response = $this->guzzleClient->request('POST', "/api/v2/authors",
                [
                    'headers' => [ 'Authorization' => "Bearer {$this->token}" ],
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

            return json_decode($response->getBody()->getContents(), true);
        }

        return [];
    }
}
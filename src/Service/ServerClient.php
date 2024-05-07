<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\Service\Attribute\Required;

final class ServerClient implements ServerClientInterface
{
    public function __construct(
        #[Required] public HttpClientInterface $httpClient,
        #[Autowire(param: 'api_server_host')] public string $apiServerHost = 'http://api_server_nginx',
    ) {
    }

    public function reportGroupUsers(?int $groupIdentifier = null): ResponseInterface
    {
        return $this->httpClient->request('GET', sprintf('%s/report/group/%d', $this->apiServerHost, $groupIdentifier));
    }

    public function listGroups(): ResponseInterface
    {
        return $this->httpClient->request('GET', sprintf('%s/groups', $this->apiServerHost));
    }

    public function createGroup(string $name): ResponseInterface
    {
        return $this->httpClient->request('POST', sprintf('%s/groups/', $this->apiServerHost), [
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept' => 'application/json',
            ],
            'body' => json_encode(['name' => $name]),
        ]);
    }

    public function changeGroupName(int $identifier, string $name): ResponseInterface
    {
        return $this->httpClient->request('PATCH', sprintf('%s/groups/%d/name', $this->apiServerHost, $identifier), [
            'headers' => [
                'Content-Type' => 'application/json-patch+json; charset=utf-8',
                'Accept' => 'application/json',
            ],
            'body' => json_encode(['name' => $name]),
        ]);
    }

    public function readGroup(int $identifier): ResponseInterface
    {
        return $this->httpClient->request('GET', sprintf('%s/groups/%d', $this->apiServerHost, $identifier));
    }

    public function deleteGroup(int $identifier): ResponseInterface
    {
        return $this->httpClient->request('DELETE', sprintf('%s/groups/%d', $this->apiServerHost, $identifier));
    }

    public function listUsers(): ResponseInterface
    {
        return $this->httpClient->request('GET', sprintf('%s/users', $this->apiServerHost));
    }

    public function createUser(string $email, string $name): ResponseInterface
    {
        return $this->httpClient->request('POST', sprintf('%s/users/', $this->apiServerHost), [
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept' => 'application/json',
            ],
            'body' => json_encode(['email' => $email, 'name' => $name]),
        ]);
    }

    public function changeUserName(int $identifier, string $name): ResponseInterface
    {
        return $this->httpClient->request('PATCH', sprintf('%s/users/%d/name', $this->apiServerHost, $identifier), [
            'headers' => [
                'Content-Type' => 'application/json-patch+json; charset=utf-8',
                'Accept' => 'application/json',
            ],
            'body' => json_encode(['name' => $name]),
        ]);
    }

    public function readUser(int $identifier): ResponseInterface
    {
        return $this->httpClient->request('GET', sprintf('%s/users/%d', $this->apiServerHost, $identifier));
    }

    public function deleteUser(int $identifier): ResponseInterface
    {
        return $this->httpClient->request('DELETE', sprintf('%s/users/%d', $this->apiServerHost, $identifier));
    }

    public function userAssignToGroup(int $identifier, int $groupIdentifier): ResponseInterface
    {
        return $this->httpClient->request('PATCH', sprintf(
            '%s/users/%d/assign-to-group/%d',
            $this->apiServerHost,
            $identifier,
            $groupIdentifier,
        ));
    }

    public function userRemoveFromGroup(int $identifier, int $groupIdentifier): ResponseInterface
    {
        return $this->httpClient->request('PATCH', sprintf(
            '%s/users/%d/remove-from-group/%d',
            $this->apiServerHost,
            $identifier,
            $groupIdentifier,
        ));
    }
}

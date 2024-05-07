<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface ServerClientInterface
{
    // Report
    public function reportGroupUsers(?int $groupIdentifier = null): ResponseInterface;

    // Groups
    public function listGroups(): ResponseInterface;

    public function createGroup(string $name): ResponseInterface;

    public function readGroup(int $identifier): ResponseInterface;

    public function changeGroupName(int $identifier, string $name): ResponseInterface;

    public function deleteGroup(int $identifier): ResponseInterface;

    // Users
    public function listUsers(): ResponseInterface;

    public function createUser(string $email, string $name): ResponseInterface;

    public function readUser(int $identifier): ResponseInterface;

    public function changeUserName(int $identifier, string $name): ResponseInterface;

    public function deleteUser(int $identifier): ResponseInterface;

    public function userAssignToGroup(int $identifier, int $groupIdentifier): ResponseInterface;

    public function userRemoveFromGroup(int $identifier, int $groupIdentifier): ResponseInterface;
}

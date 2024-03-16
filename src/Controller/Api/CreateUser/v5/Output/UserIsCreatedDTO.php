<?php

namespace App\Controller\Api\CreateUser\v5\Output;

use App\DTO\Traits\SafeLoadFieldsTrait;

class UserIsCreatedDTO
{
    use SafeLoadFieldsTrait;

    public int $id;

    public string $login;


    public bool $isActive;

    public function getSafeFields(): array
    {
        return ['id', 'login', 'isActive'];
    }
}

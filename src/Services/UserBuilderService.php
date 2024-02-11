<?php

namespace App\Services;

use App\Entity\User;
use App\Managers\MeetManager;
use App\Managers\UserManager;

class UserBuilderService
{
    public function __construct(
        private readonly MeetManager $meetManager,
        private readonly UserManager $userManager,
    ) {
    }

/**
 * @param string[] $texts
 */
public function createUserWithMeets(string $login, array $texts): User
{
    $user = $this->userManager->create($login);
    foreach ($texts as $text) {
        $this->meetManager->postMeet($user, $text);
    }

    return $user;
}
}
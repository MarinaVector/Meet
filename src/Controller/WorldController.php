<?php

namespace App\Controller;

use App\Entity\User;
use App\Managers\UserManager;
use App\Services\UserBuilderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class WorldController extends AbstractController
{

    public function __construct(private readonly UserBuilderService $userBuilderService)
    {
    }

    public function hello(): Response
    {
        /*
        $user = $this->userBuilderService->createUserWithMeets(
            'mr Mkien',
            ['online'],
            ['let s go to sports!!!']
        );

        return $this->json($user->toArray());
        */
        $users = $this->userBuilderService->createUserWithFollower(
        'J.R.R. Tolkien',
        'Ivan Ivanov'
    );

        return $this->json(array_map(static fn(User $user) => $user->toArray(), $users));

    }
}
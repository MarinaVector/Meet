<?php

namespace App\Controller;

use App\Services\UserBuilderService;
use App\Managers\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class WorldController extends AbstractController
{

    public function __construct(private readonly UserBuilderService $userBuilderService)
    {
    }

    public function hello(): Response
    {
        $user = $this->userBuilderService->createUserWithMeets(
            'mr Mkien',
            ['online'],
            ['let s go to sports!!!']
        );
//return 'vvv';
        return $this->json($user->toArray());
    }
}
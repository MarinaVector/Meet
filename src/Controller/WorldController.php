<?php

namespace App\Controller;

use App\Entity\User;
use App\Managers\UserManager;
use App\Services\UserBuilderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class WorldController extends AbstractController
{

    public function __construct(private readonly UserManager $userManager)
    {
    }

    public function hello(): Response
    {
        $users = $this->userManager->findUsersByLogin('J.R.R. Tolkien');

        return $this->json(array_map(static fn(User $user) => $user->toArray(), $users));
    }
}
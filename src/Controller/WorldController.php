<?php

namespace App\Controller;

use App\Services\UserBuilderService;
use App\Managers\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class WorldController extends AbstractController
{

    public function hello(UserManager $userManager): Response
    {
        $user = $userManager->create('My user');

        return $this->json($user->toArray());
    }
}
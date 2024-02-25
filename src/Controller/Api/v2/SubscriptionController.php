<?php

namespace App\Controller\Api\v2;

use App\Entity\Subscription;
use App\Entity\User;
use App\Managers\SubscriptionManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v2/subscription')]
class SubscriptionController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(path: '', methods: ['POST'])]
    public function saveSubscriptionAction(Request $request): Response
    {
        $author_id = $request->request->get('author_id');
        $follower_id = $request->request->get('follower_id');

        $author = $this->entityManager->getRepository(User::class)->find($author_id);
        $follower = $this->entityManager->getRepository(User::class)->find($follower_id);

        if (!$author || !$follower) {
            return $this->json(['error' => 'Author or Follower not found'], Response::HTTP_NOT_FOUND);
        }

        $subscription = new Subscription();
        $subscription->setCreatedAt();
        $subscription->setUpdatedAt();
        $subscription->setAuthor($author);
        $subscription->setFollower($follower);

       $this->entityManager->persist($subscription);
       $this->entityManager->flush();

        return $this->json(['success' => 'Subscription created successfully'], Response::HTTP_CREATED);
    }

    #[Route(path: '/{id}', methods: ['GET'], )]
    public function showUserSubscriptions(int $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $subscriptions = $user->getSubscriptions();

        $subscriptionsData = [];
        foreach ($subscriptions as $subscription) {
            $subscriptionsData[] = [
                'id' => $subscription->getId(),
                'author_id' => $subscription->getAuthor()->getId(),
                'follower_id' => $subscription->getFollower()->getId(),

            ];
        }

        return $this->json($subscriptionsData);
    }


}

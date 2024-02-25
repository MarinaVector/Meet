<?php

namespace App\Managers;

use App\Entity\Subscription;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }


    public function create(User $author, User $follower): Subscription {
        $subscription = new Subscription();
        $subscription->setAuthor($author);
        $subscription->setFollower($follower);
        $author->addSubscriptionFollower($subscription);
        $follower->addSubscriptionAuthor($subscription);
        $subscription->setCreatedAt();
        $subscription->setUpdatedAt();

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();

        return $subscription;
    }

    // In SubscriptionRepository or a related service
    public function findSubscriptionsWithUser(User $user): array {
        // Assuming the use of Doctrine's EntityManager
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('subscription')
            ->from(Subscription::class, 'subscription')
            ->where('subscription.author = :user OR subscription.follower = :user')
            ->setParameter('user', $user);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return User[]
     */
    public function getSubscriptions(int $page, int $perPage): array
    {
        /** @var SubscriptionRepository $subscriptionRepository */
        $subscriptionRepository = $this->entityManager->getRepository(Subscription::class);

        return $subscriptionRepository->getSubscriptions($page, $perPage);
    }



}
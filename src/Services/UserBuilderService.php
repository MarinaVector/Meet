<?php

namespace App\Services;

use App\Entity\User;
use App\Managers\MeetManager;
use App\Managers\UserManager;
use App\Managers\SubscriptionManager;

class UserBuilderService
{
    public function __construct(
        private readonly MeetManager $meetManager,
        private readonly UserManager $userManager,
        private readonly SubscriptionManager $subscriptionManager,
    ) {
    }

    /**
     * @param string[] $texts
     */
    public function createUserWithMeets(string $login, array $texts, array $formats): User
    {
        $user = $this->userManager->create($login);
        $count = count($texts);

      //  foreach ($texts as $text) {
       //     $this->meetManager->postMeet($user, $text);
      //  }
        for ($i=0;$i<$count;$i++ )
        {
            $this->meetManager->postMeet($user, $formats[$i], $texts[$i]);
                   }
        return $user;
            $userId = $user->getId();
            $this->userManager->clearEntityManager();

            return $this->userManager->findUser($userId);
        }
    public function createUserWithFollower(string $login, string $followerLogin): array
    {
        $user = $this->userManager->create($login);
        $follower = $this->userManager->create($followerLogin);
        $this->userManager->subscribeUser($user, $follower);
        $this->subscriptionManager->addSubscription($user, $follower);

        return [$user, $follower];
    }


}
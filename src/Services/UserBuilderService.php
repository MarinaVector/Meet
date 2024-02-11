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
    )
    {
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
var_dump( $count);
            $userId = $user->getId();
            $this->userManager->clearEntityManager();

            return $this->userManager->findUser($userId);
        }

}
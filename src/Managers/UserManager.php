<?php
namespace App\Managers;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

public function create(string $login): User
{
    $user = new User();
    $user->setLogin($login);
    $this->entityManager->persist($user);
    $this->entityManager->flush();

    return $user;
}

public function clearEntityManager(): void
{
    $this->entityManager->clear();
}

public function findUser(int $id): ?User
{
    $repository = $this->entityManager->getRepository(User::class);
    $user = $repository->find($id);

    return $user instanceof User ? $user : null;
}

public function subscribeUser(User $author, User $follower): void
{
    $author->addFollower($follower);
    $follower->addAuthor($author);
    $this->entityManager->flush();
}

/**
 * @return User[]
 */
public function findUsersByLogin(string $name): array
{
    return $this->entityManager->getRepository(User::class)->findBy(['login' => $name]);
}

public function updateUserLogin(int $userId, string $login): ?User
{
        $user = $this->findUser($userId);
        if (!($user instanceof User)) {
            return null;
        }
        $user->setLogin($login);
        $this->entityManager->flush();

        return $user;
}

}

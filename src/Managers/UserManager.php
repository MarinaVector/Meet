<?php

namespace App\Managers;

use App\DTO\ManageUserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

class UserManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createByLogin(string $login): User
    {
        $user = new User();
        $user->setLogin($login);
        $user->setCreatedAt();
        $user->setUpdatedAt();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function saveUser(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
    public function saveUserFromDTO(User $user, ManageUserDTO $manageUserDTO): ?int
    {
        $user->setLogin($manageUserDTO->login);
        $user->setPassword($manageUserDTO->password);
        $user->setIsActive($manageUserDTO->isActive);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user->getId();
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

    /**
     * @return User[]
     */
    public function findUsersByCriteria(string $login): array
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()?->eq('login', $login));
        /** @var EntityRepository $repository */
        $repository = $this->entityManager->getRepository(User::class);

        return $repository->matching($criteria)->toArray();
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

    public function findUsersWithQueryBuilder(string $login): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        // SELECT u.* FROM `user` u WHERE u.login LIKE :userLogin
        $queryBuilder->select('u')
            ->from(User::class, 'u')
            ->andWhere($queryBuilder->expr()->like('u.login',':userLogin'))
            ->setParameter('userLogin', "%$login%");

        return $queryBuilder->getQuery()->getResult();
    }

    public function updateUserLoginWithQueryBuilder(int $userId, string $login): void
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->update(User::class,'u')
            ->set('u.login', ':userLogin')
            ->where($queryBuilder->expr()->eq('u.id', ':userId'))
            ->setParameter('userId', $userId)
            ->setParameter('userLogin', $login);

        $queryBuilder->getQuery()->execute();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function updateUserLoginWithDBALQueryBuilder(int $userId, string $login): void
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();
        $queryBuilder->update('"user"','u')
            ->set('login', ':userLogin')
            ->where($queryBuilder->expr()->eq('u.id', ':userId'))
            ->setParameter('userId', $userId)
            ->setParameter('userLogin', $login);

        $queryBuilder->executeStatement();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findUserWithMeetsWithQueryBuilder(int $userId): ?User
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('u', 't')
            ->from(User::class, 'u')
            ->leftJoin('u.meets', 't')
            ->where($queryBuilder->expr()->eq('u.id', ':userId'))
            ->setParameter('userId', $userId);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function findUserWithMeetsWithDBALQueryBuilder(int $userId): array
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();
        $queryBuilder->select('u', 't')
            ->from('"user"', 'u')
            ->leftJoin('u', 'meet', 't', 'u.id = t.author_id')
            ->where($queryBuilder->expr()->eq('u.id', ':userId'))
            ->setParameter('userId', $userId);

        return $queryBuilder->executeQuery()->fetchAllNumeric();
    }

    /**
     * @return User[]
     */
    public function getUsers(int $page, int $perPage): array
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        return $userRepository->getUsers($page, $perPage);
    }

    public function deleteUser(User $user): bool
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return true;
    }

    public function deleteUserById(int $userId): bool
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);
        /** @var User $user */
        $user = $userRepository->find($userId);
        if ($user === null) {
            return false;
        }
        return $this->deleteUser($user);
    }
}
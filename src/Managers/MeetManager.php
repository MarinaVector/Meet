<?php

namespace App\Managers;

use App\Entity\Meet;
use App\Entity\User;
use App\Repository\MeetRepository;
use Doctrine\ORM\EntityManagerInterface;

class MeetManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(string $text,string $format ): Meet
    {
        $meet = new Meet();
        $meet->setText($text);
        $meet->setFormat($format);
        $meet->setCreatedAt();
        $meet->setUpdatedAt();

        $this->entityManager->persist($meet);
        $this->entityManager->flush();

        return $meet;
    }

    public function postMeet(User $author, string $text, string $format): Meet
    {
        $meet = new Meet();
        $meet->setAuthor($author);
        $meet->setText($text);
        $meet->setFormat($format);
        $meet->setCreatedAt();
        $meet->setUpdatedAt();
        $author->addMeet($meet);
        $this->entityManager->persist($meet);
        $this->entityManager->flush();
        return $meet;

    }

    public function clearEntityManager(): void
    {
        $this->entityManager->clear();
    }

    public function findMeet(int $id): ?Meet
    {
        $repository = $this->entityManager->getRepository(Meet::class);
        $meet = $repository->find($id);

        return $meet instanceof Meet ? $meet : null;
    }

    public function updateMeetText(int $id, string $text): ?Meet
    {
        $meet = $this->findMeet($id);
        if (!($meet instanceof Meet)) {
            return null;
        }
        $meet->setText($text);
        $this->entityManager->flush();

        return $meet;
    }

    /**
     * @return Meet[]
     */
    public function findMeetsByFormat(string $name): array
    {
        return $this->entityManager->getRepository(Meet::class)->findBy(['format' => $name]);
    }



    /**
     * @return Meet[]
     */
    public function getMeets(int $page, int $perPage): array
    {
        /** @var MeetRepository $meetRepository */
        $meetRepository = $this->entityManager->getRepository(Meet::class);

        return $meetRepository->getMeets($page, $perPage);
    }

    public function deleteMeet(Meet $meet): bool
    {
        $this->entityManager->remove($meet);
        $this->entityManager->flush();

        return true;
    }

    public function deleteMeetById(int $meetId): bool
    {
        /** @var MeetRepository $meetRepository */
        $meetRepository = $this->entityManager->getRepository(Meet::class);
        /** @var Meet $user */
        $meet = $meetRepository->find($meetId);
        if ($meet === null) {
            return false;
        }
        return $this->deleteMeet($meet);
    }
}
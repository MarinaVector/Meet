<?php

namespace App\Managers;

use App\Entity\Meet;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class MeetManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

public function postMeet(User $author, string $text): void
{
    $meet = new Meet();
    $meet->setAuthor($author);
    $meet->setText($text);
    $meet->setFormat($format);
    $meet>setCreatedAt();
    $meet->setUpdatedAt();
    $this->entityManager->persist($meet);
    $this->entityManager->flush();
}
}
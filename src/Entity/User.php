<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;

#[ORM\Table(name: '`user`')]
#[ORM\Entity]
class User
{
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: 'User', mappedBy: 'followers')]
    private Collection $authors;



    public function __construct()
    {
        $this->meets = new ArrayCollection();
        $this->authors = new ArrayCollection();
        $this->followers = new ArrayCollection();
    }

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'authors')]
    #[ORM\JoinTable(name: 'author_follower')]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'follower_id', referencedColumnName: 'id')]
    private Collection $followers;

    #[ORM\Column(type: 'string', length: 32, nullable: false)]
    private string $login;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: 'Meet')]
    private Collection $meets;


    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;
    public function addMeet(Meet $meet): void
    {
        if (!$this->meets->contains($meet)) {
            $this->meets->add($meet);
        }
    }

    public function addFollower(User $follower): void
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
        }
    }

    public function addAuthor(User $author): void
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function setCreatedAt(): void {
        $this->createdAt = new DateTime();
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): void {
        $this->updatedAt = new DateTime();
    }
    #[ArrayShape([
        'id' => 'int|null',
        'login' => 'string',
        'createdAt' => 'string',
        'updatedAt' => 'string',
        'meets' => ['id' => 'int|null', 'login' => 'string', 'createdAt' => 'string', 'updatedAt' => 'string'],
        'followers' => 'string[]',
        'authors' => 'string[]'
    ])]

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
            'meets' => array_map(static fn(Meet $meet) => $meet->toArray(), $this->meets->toArray()),
            'followers' => array_map(static fn(User $user) => $user->getLogin(), $this->followers->toArray()),
            'authors' => array_map(static fn(User $user) => $user->getLogin(), $this->authors->toArray()),
        ];
    }
}
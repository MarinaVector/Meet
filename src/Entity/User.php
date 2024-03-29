<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: '`user`')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements HasMetaTimestampsInterface
{
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private string $id;

    #[ORM\Column(type: 'string', length: 32, nullable: false)]
    private string $login;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    #[Timestampable(on: 'create')]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    #[Timestampable(on: 'update')]
    private DateTime $updatedAt;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: 'Meet')]
    private Collection $meets;

    #[ORM\ManyToMany(targetEntity: 'User', mappedBy: 'followers')]
    private Collection $authors;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'authors')]
    #[ORM\JoinTable(name: 'author_follower')]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'follower_id', referencedColumnName: 'id')]
    private Collection $followers;

    #[ORM\OneToMany(mappedBy: 'follower', targetEntity: 'Subscription')]
    private Collection $subscriptionAuthors;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: 'Subscription')]
    private Collection $subscriptionFollowers;

    #[ORM\Column(type: 'string', length: 32, nullable: false)]
    private string $password;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $isActive;

    public function __construct()
    {
        $this->meets = new ArrayCollection();
        $this->authors = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->subscriptionAuthors = new ArrayCollection();
        $this->subscriptionFollowers = new ArrayCollection();
    }

    public function addSubscriptionAuthor(Subscription $subscription): void
    {
        if (!$this->subscriptionAuthors->contains($subscription)) {
            $this->subscriptionAuthors->add($subscription);
        }
    }


    public function addSubscriptionFollower(Subscription $subscription): void
    {
        if (!$this->subscriptionFollowers->contains($subscription)) {
            $this->subscriptionFollowers->add($subscription);
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

    public function addMeet(Meet $meet): void
    {
        if (!$this->meets->contains($meet)) {
            $this->meets->add($meet);
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


    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }


    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    #[ArrayShape([
        'id' => 'int|null',
        'login' => 'string',
        'createdAt' => 'string',
        'updatedAt' => 'string',
        'meets' => ['id' =>'int|null', 'login' => 'string', 'createdAt' => 'string', 'updatedAt' => 'string'],
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
            'followers' => array_map(
                static fn(User $user) => ['id' => $user->getId(), 'login' => $user->getLogin()],
                $this->followers->toArray()
            ),
            'authors' => array_map(
                static fn(User $user) => ['id' => $user->getId(), 'login' => $user->getLogin()],
                $this->authors->toArray()
            ),
            'subscriptionFollowers' => array_map(
                static fn(Subscription $subscription) => [
                    'subscriptionId' => $subscription->getId(),
                    'userId' => $subscription->getFollower()->getId(),
                    'login' => $subscription->getFollower()->getLogin(),
                ],
                $this->subscriptionFollowers->toArray()
            ),
            'subscriptionAuthors' => array_map(
                static fn(Subscription $subscription) => [
                    'subscriptionId' => $subscription->getId(),
                    'userId' => $subscription->getAuthor()->getId(),
                    'login' => $subscription->getAuthor()->getLogin(),
                ],
                $this->subscriptionAuthors->toArray()
            ),
        ];
    }

    /**
     * @return User[]
     */
    public function getFollowers(): array
    {
        return $this->followers->toArray();
    }
}
<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\ORM\EntityRepository;

class SubscriptionRepository extends EntityRepository
{
    /**
     * @return Subscription[]
     */
    public function getSubscriptions(int $page, int $perPage): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('t')
            ->from($this->getClassName(), 't')
            ->orderBy('t.id', 'DESC')
            ->setFirstResult($perPage * $page)
            ->setMaxResults($perPage);

        return $qb->getQuery()->getResult();
    }
}

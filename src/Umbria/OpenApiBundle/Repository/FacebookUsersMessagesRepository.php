<?php

namespace Umbria\OpenApiBundle\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class FacebookUsersMessagesRepository extends EntityRepository
{

    public function findLastUserMessage($sender)
    {
        return $this->findOneBy(array('sender' => $sender), array('timeStamp' => 'DESC'));
    }

    public function findLastUserPosition($sender)
    {
        /**@var \Doctrine\ORM\Query $query */
        $query = $this->createQueryBuilder('m')
            ->where('m.lat IS NOT NULL')
            ->andWhere('m.long IS NOT NULL')
            ->andWhere('m.sender = :sender')
            ->orderBy('m.timeStamp', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->setParameter('sender', $sender)
            ->getQuery();
        return $query->getOneOrNullResult();
    }
}
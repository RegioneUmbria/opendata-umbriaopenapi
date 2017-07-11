<?php

namespace Umbria\OpenApiBundle\Repository\Tourism\GraphsEntitiesInnerObjects\EventDescription;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

/**
 * TravelAgencyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventDescription extends EntityRepository
{

    /**
     * @param $date \DateTime
     * @param null $em
     */
    public function removeLastUpdatedBefore($date, $em = null)
    {
        $criteria = new Criteria();
        $criteria->where($criteria->expr()->lt('lastUpdateAt', $date));
        foreach ($this->matching($criteria) as $agency) {
            $this->getEntityManager()->remove($agency);
        }
        if ($em == null) {
            $em = $this->getEntityManager();
        }
        $em->flush();
    }

    public function findById($id)
    {
        $qb = $this->createQueryBuilder("o");
        $qb->where($qb->expr()->like("o.uri", "?1"));
        $qb->setParameter(1, "%/" . $id);
        return $qb->getQuery()->getResult();
    }
}

<?php

namespace Umbria\OpenApiBundle\Repository\Tourism\GraphsEntities;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Umbria\OpenApiBundle\Entity\Tourism\GraphsEntities\Attractor;

/**
 * AttractorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AttractorRepository extends EntityRepository
{
    /**
     * @param $date \DateTime
     * @param null $em
     */
    public function removeLastUpdatedBefore($date, $em = null)
    {
        $criteria = new Criteria();
        $criteria->where($criteria->expr()->lt('lastUpdateAt', $date));
        foreach ($this->matching($criteria) as $attractor) {
            $this->getEntityManager()->remove($attractor);
        }
        if ($em == null) {
            $em = $this->getEntityManager();
        }
        $em->flush();
    }

    /**
     * @param $id
     * @return Attractor
     */
    public function findById($id)
    {
        $qb = $this->createQueryBuilder("o");
        $qb->where($qb->expr()->like("o.uri", "?1"));
        $qb->setParameter(1, "%/" . $id);
        return $qb->getQuery()->getResult();
    }

    /**
     * @param $latMax
     * @param $latMin
     * @param $lngMax
     * @param $lngMin
     * @return Attractor[]
     */
    public function findByPosition($latMax, $latMin, $lngMax, $lngMin)
    {
        $qb = $this->createQueryBuilder("a");
        if ($latMax != null ||
            $latMin != null ||
            $lngMax != null ||
            $lngMin != null
        ) {
            if ($latMax != null) {
                $qb = $qb->andWhere(
                    $qb->expr()->lte("a.lat", ':latMax'),
                    $qb->expr()->isNotNull("a.lat"),
                    $qb->expr()->gt("a.lat", ':empty')
                )
                    ->setParameter('latMax', $latMax)
                    ->setParameter('empty', '0');
            }
            if ($latMin != null) {
                $qb = $qb->andWhere(
                    $qb->expr()->gte("a.lat", ':latMin'),
                    $qb->expr()->isNotNull("a.lat"),
                    $qb->expr()->gt("a.lat", ":empty")
                )
                    ->setParameter('latMin', $latMin)
                    ->setParameter('empty', '0');
            }
            if ($lngMax != null) {
                $qb = $qb->andWhere(
                    $qb->expr()->lte("a.lng", ':lngMax'),
                    $qb->expr()->isNotNull("a.lng"),
                    $qb->expr()->gt("a.lng", ":empty")
                )
                    ->setParameter('lngMax', $lngMax)
                    ->setParameter('empty', '0');
            }
            if ($lngMin != null) {
                $qb = $qb->andWhere(
                    $qb->expr()->gte("a.lng", ':lngMin'),
                    $qb->expr()->isNotNull("a.lng"),
                    $qb->expr()->gt("a.lng", ":empty")
                )
                    ->setParameter('lngMin', $lngMin)
                    ->setParameter('empty', '0');
            }
        }
        return $qb->getQuery()->getResult();
    }

}

<?php

namespace Gushh\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PromotionExceptionPeriodRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PromotionExceptionPeriodRepository extends EntityRepository
{
    /**
     * Get all exceptions for promotion
     *
     * @return array
     */
    public function findByPromo($pid)
    {
        return $this->getEntityManager()
            ->getRepository('GushhCoreBundle:PromotionExceptionPeriod')
            ->createQueryBuilder('pep')
            ->where('pep.promotion = :promotion')
            ->setParameter('promotion.id', $pid)
            ->orderBy('pep.periodFrom', 'ASC');
    }
}

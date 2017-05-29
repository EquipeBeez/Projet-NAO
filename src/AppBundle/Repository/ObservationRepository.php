<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ObservationRepository extends EntityRepository
{
    public function getAll()
    {
        $qb = $this->createQueryBuilder('observation')->getQuery();
        return $qb;
    }

    public function findObsWithStatus($status)
    {
        $qb = $this->createQueryBuilder('observation');
        $qb->where('observation.status = :status')
            ->setParameter('status', $status)
            ->orderBy('observation.dateObservation', 'DESC')
        ;
        return $qb->getQuery();
    }

    public function findLastObservations($nombre)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where('a.status = :status')
            ->setParameter('status', 'validated')
            ->orderBy('a.dateObservation', 'DESC')
            ->setMaxResults($nombre)
        ;
        return $qb->getQuery()->getResult();
    }

}

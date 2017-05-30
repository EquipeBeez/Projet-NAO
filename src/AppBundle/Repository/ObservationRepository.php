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


    public function findObsWithAllStatus()
    {
        $qb = $this->createQueryBuilder('observation');
        $qb ->join('observation.espece', 'espece')
            ->join('observation.author', 'author')
            ->orderBy('observation.dateObservation', 'DESC')
        ;
        return $qb->getQuery();
    }

    public function findObsWithStatus($status)
    {
        $qb = $this->createQueryBuilder('observation');
        $qb->where('observation.status = :status')
            ->join('observation.espece', 'espece')
            ->join('observation.author', 'author')
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

    public function findObservationByLike($term)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('o')
            ->from('AppBundle:Observation', 'o')
            ->join('o.espece', 'e')
            ->join('o.author', 'a')
            ->Where('o.title LIKE :terms')
            ->orWhere('o.dateObservation LIKE :terms')
            ->orWhere('e.LbNom LIKE :terms')
            ->orWhere('e.NomVern LIKE :terms')
            ->orWhere('a.name LIKE :terms')
            ->andWhere('o.status = :status')
            ->setParameter('terms', '%' . $term .'%' )
            ->setParameter('status', 'validated' );

        return $qb->getQuery()->getResult();
    }
}

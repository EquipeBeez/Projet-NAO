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
        $qb->select('observation')
            ->from('AppBundle:Observation', 'observation')
            ->join('observation.espece', 'espece')
            ->join('observation.author', 'author')
            ->Where('observation.title LIKE :terms')
            ->orWhere('observation.dateObservation LIKE :terms')
            ->orWhere('espece.LbNom LIKE :terms')
            ->orWhere('espece.NomVern LIKE :terms')
            ->orWhere('author.name LIKE :terms')
            ->andWhere('observation.status = :status')
            ->setParameter('terms', '%' . $term .'%' )
            ->setParameter('status', 'validated' );

        return $qb->getQuery()->getResult();
    }
    public function findObservationByLikeWithoutStatus($term)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('observation')
            ->from('AppBundle:Observation', 'observation')
            ->join('observation.espece', 'espece')
            ->join('observation.author', 'author')
            ->Where('observation.title LIKE :terms')
            ->orWhere('observation.dateObservation LIKE :terms')
            ->orWhere('espece.LbNom LIKE :terms')
            ->orWhere('espece.NomVern LIKE :terms')
            ->orWhere('author.name LIKE :terms')
            ->setParameter('terms', '%' . $term .'%' );

        return $qb->getQuery()->getResult();
    }
}

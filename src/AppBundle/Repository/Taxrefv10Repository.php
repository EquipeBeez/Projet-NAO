<?php

namespace AppBundle\Repository;

/**
 * Taxrefv10Repository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Taxrefv10Repository extends \Doctrine\ORM\EntityRepository
{

    public function getAll()
    {
        $qb = $this->createQueryBuilder('taxrefv10')->getQuery();
        return $qb;
    }

    public function findSpeciesByLike($term)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from('AppBundle:Taxrefv10', 's')
            ->Where('s.LbNom LIKE :terms')
            ->orWhere('s.LbAuteur LIKE :terms')
            ->orWhere('s.NomVern LIKE :terms')
            ->orWhere('s.Famille LIKE :terms')
            ->orWhere('s.NomVernEng LIKE :terms')
            ->setParameter('terms', '%' . $term .'%' );
        return $qb->getQuery()->getResult();
    }

}

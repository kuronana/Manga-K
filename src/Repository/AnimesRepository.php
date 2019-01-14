<?php

namespace App\Repository;

use App\Entity\Animes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Animes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Animes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Animes[]    findAll()
 * @method Animes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnimesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Animes::class);
    }

    // /**
    //  * @return Animes[] Returns an array of Animes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Animes
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

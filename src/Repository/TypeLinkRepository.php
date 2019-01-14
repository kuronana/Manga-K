<?php

namespace App\Repository;

use App\Entity\TypeLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TypeLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeLink[]    findAll()
 * @method TypeLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeLinkRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TypeLink::class);
    }

    // /**
    //  * @return TypeLink[] Returns an array of TypeLink objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypeLink
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

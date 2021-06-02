<?php

namespace App\Repository;

use App\Entity\DeliveryNotifications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeliveryNotifications|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeliveryNotifications|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeliveryNotifications[]    findAll()
 * @method DeliveryNotifications[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeliveryNotificationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeliveryNotifications::class);
    }

    // /**
    //  * @return DeliveryNotifications[] Returns an array of DeliveryNotifications objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DeliveryNotifications
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

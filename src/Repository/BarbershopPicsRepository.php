<?php

namespace App\Repository;

use App\Entity\BarbershopPics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BarbershopPics>
 *
 * @method BarbershopPics|null find($id, $lockMode = null, $lockVersion = null)
 * @method BarbershopPics|null findOneBy(array $criteria, array $orderBy = null)
 * @method BarbershopPics[]    findAll()
 * @method BarbershopPics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BarbershopPicsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BarbershopPics::class);
    }

    public function save(BarbershopPics $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BarbershopPics $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return BarbershopPics[] Returns an array of BarbershopPics objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BarbershopPics
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

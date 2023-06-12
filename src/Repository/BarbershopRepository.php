<?php

namespace App\Repository;

use App\Entity\Barbershop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Barbershop>
 *
 * @method Barbershop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Barbershop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Barbershop[]    findAll()
 * @method Barbershop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BarbershopRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Barbershop::class);
    }

    public function save(Barbershop $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Barbershop $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getLastThreeValidBarbershop()
    {
        $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();
            $query = 
                $qb->select('b')
                ->from('App\Entity\Barbershop', 'b')
                ->where('b.validate = true')
                ->orderBy('b.creationDate', 'DESC')
                ->setMaxResults(3)
                ->getQuery();
    
            return $query->getResult();
    }

    public function getAllValidBarbershop()
    {
        $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();
            $query = 
                $qb->select('b')
                ->from('App\Entity\Barbershop', 'b')
                ->where('b.validate = true')
                ->orderBy('b.nom', 'ASC')
                ->getQuery();
    
            return $query->getResult();
    }

//    /**
//     * @return Barbershop[] Returns an array of Barbershop objects
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

//    public function findOneBySomeField($value): ?Barbershop
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

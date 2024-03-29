<?php

namespace App\Repository;

use App\Model\SearchData;
use App\Entity\Barbershop;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginatorInterface)
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

    public function getAllCities()
    {
        $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();
            $query = 
                $qb->select('DISTINCT b.ville')
                ->from('App\Entity\Barbershop', 'b')
                ->where('b.validate = true')
                ->orderBy('b.ville', 'ASC')
                ->getQuery();
        
                

            $cities = [];
            foreach ($query->getResult() as $row) {
                $cities[] = $row['ville'];
            }

        return $cities;
    }

    

    public function findBySearch( SearchData $searchData): Array {

        //On selectionne la table barbier, et on cible les barbiers validés
        $data = $this->createQueryBuilder('b')
            ->where('b.validate = 1');

        // Si il y a une recherche
        if (!empty($searchData->q)) {
            $data = $data
                ->andWhere('b.nom LIKE :q')
                ->setParameter('q', "%{$searchData->q}%");
        }

        // Si un filtre est appliqué 
        if (!empty($searchData->sortBy)) {
            if ($searchData->sortBy === 'likes') {
                $data = $data->leftJoin('b.likes', 'l')
                ->addGroupBy('b.id')
                ->addOrderBy('COUNT(l.id)', 'DESC');
            } elseif ($searchData->sortBy === 'comments') {
                $data = $data->leftJoin('b.avis', 'a')
                ->addGroupBy('b.id')
                ->addOrderBy('COUNT(a.id)', 'DESC');           
            }
        }

        if (!empty($searchData->city)) {
            $data = $data
                ->andWhere('b.ville LIKE :city')
                ->setParameter('city', "%{$searchData->city}%");
        }

        $data = $data
            ->getQuery()
            ->getResult();

        return $data;
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

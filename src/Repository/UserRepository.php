<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

    // Retourne les barbershop likés par l'User
    public function getLikedBarbershops($user)
        {
            $em = $this->getEntityManager();

            $qb = $em->createQueryBuilder();
            
            
            $query = 
                // On selectionne les barbershops b
                $qb->select('b')
                // Depuis l'entité barbershop, on donne l'alias b
                ->from('App\Entity\Barbershop', 'b')
                // Join de la table barbershop_user_likes
                ->innerJoin('b.likes', 'user')
                // Ou user id est égal au user passé en paramètre
                ->where('user.id = :user')
                ->setParameter('user', $user)
                ->getQuery();
    
            return $query->getResult();
        }

        // Récuperer les rendez vous à venir
        public function getUpcomingRendezVous($user)
        {
            $em = $this->getEntityManager();

            $qb = $em->createQueryBuilder();
        
            $query = 
                // On selectionne les barbershops b
                $qb->select('r')
                // Depuis l'entité barbershop, on donne l'alias b
                ->from('App\Entity\RendezVous', 'r')
                // Ou user id est égal au user passé en paramètre
                ->where('r.debut > :currentdate')
                ->andWhere('r.user = :user')
                ->setParameter('currentdate', new \DateTime())
                ->setParameter('user', $user)
                ->getQuery();
    
            return $query->getResult();
        }

        // Récuperer les rendez vous passés
        public function getPastRendezVous($user)
        {
            $em = $this->getEntityManager();

            $qb = $em->createQueryBuilder();
        
            $query = 
                // On selectionne les barbershops b
                $qb->select('r')
                // Depuis l'entité barbershop, on donne l'alias b
                ->from('App\Entity\RendezVous', 'r')
                // Ou user id est égal au user passé en paramètre
                ->where('r.debut < :currentdate')
                ->andWhere('r.user = :user')
                ->setParameter('currentdate', new \DateTime())
                ->setParameter('user', $user)
                ->getQuery();
    
            return $query->getResult();
        }

        // Récuperer le dernier RDV
        public function getLastRendezVous($user)
        {
            $em = $this->getEntityManager();

            $qb = $em->createQueryBuilder();

            $query =
                $qb->select('r')
                    ->from('App\Entity\RendezVous', 'r')
                    ->where('r.debut > :currentdate')
                    ->andWhere('r.user = :user')
                    ->setParameter('currentdate', new \DateTime())
                    ->setParameter('user', $user)
                    ->orderBy('r.debut', 'DESC') // Tri par ordre décroissant de la date de début
                    ->setMaxResults(1) // Limite les résultats à un seul rendez-vous
                    ->getQuery();

            return $query->getSingleResult();
        }

        

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

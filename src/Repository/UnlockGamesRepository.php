<?php

namespace App\Repository;

use App\Entity\UnlockGames;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UnlockGames|null find($id, $lockMode = null, $lockVersion = null)
 * @method UnlockGames|null findOneBy(array $criteria, array $orderBy = null)
 * @method UnlockGames[]    findAll()
 * @method UnlockGames[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnlockGamesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UnlockGames::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(UnlockGames $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(UnlockGames $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

   
    public function findByUserQr($value)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT u.id , u.id_user_id , u.finish, u.qr_code_id, u.date , q.id_game_id , q.time , g.name  FROM unlock_games u
            LEFT JOIN qr_code AS q ON ( q.id = u.qr_code_id )
            LEFT JOIN games AS g ON ( g.id = q.id_game_id )  
            WHERE u.id_user_id = :val
            ORDER BY u.id ASC
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['val' => $value]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function findUnlockedr($idUser , $idGame)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * , q.id_game_id   FROM unlock_games u
            INNER JOIN qr_code AS q ON ( q.id = u.qr_code_id )
            WHERE u.id_user_id = :val AND q.id_game_id = :game
            LIMIT 1
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['val' => $idUser , 'game' => $idGame]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }
    

    /*
    public function findOneBySomeField($value): ?UnlockGames
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

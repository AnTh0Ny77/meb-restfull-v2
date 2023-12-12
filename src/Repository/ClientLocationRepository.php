<?php
namespace App\Repository;

use App\Entity\ClientLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @method ClientLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientLocation[]    findAll()
 * @method ClientLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientLocation::class);
    }

     /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ClientLocation $entity, bool $flush = true): void
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
    public function remove(ClientLocation $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function resetBooleanColumnsForUser($userId)
    {
        return $this->createQueryBuilder('cl')
            ->update()
            ->set('cl.booleanColumn', '0')
            ->where('cl.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }

    
}

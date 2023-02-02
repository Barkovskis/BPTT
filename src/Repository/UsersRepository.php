<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Users>
 *
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    /**
     * @return Users[] Returns an array of Users objects
     *
     * Native querry
     * SELECT u.name as name, COUNT(b.id) as bcount, inv.name
     * FROM users as u
     * LEFT JOIN books as b on b.user_id = u.id
     * LEFT JOIN users as inv ON inv.id = u.invited_by_user_id
     * GROUP BY u.name
     * ORDER BY u.id ASC;
     */
    public function getAllUsersData(): array
    {
       return $this->createQueryBuilder('u')
           ->select('u.name as name', 'COUNT(b.id) as bcount', 'inv.name as iname')
           ->leftJoin(
               'App\Entity\Books',
               'b',
               Join::WITH,
               'b.user_id = u.id'
           )
           ->leftJoin(
               'App\Entity\Users',
               'inv',
               Join::WITH,
               'inv.id = u.invited_by_user_id'
           )
           ->groupBy('u.name')
           ->orderBy('u.id', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
        ;
    }

    /**
     * @return Users[] Returns an array of Users objects
     *
     * Native querry
     *
     * SELECT u.name,
     * ( SELECT count(invited.id)
     * FROM users invited
     * WHERE invited.invited_by_user_id = u.id
     * ) as icount,
     * COUNT(b.id) as bcount,
     * SUM(b.rating) as rating
     * FROM users u
     * INNER JOIN USERS invited ON u.id = invited.invited_by_user_id
     * INNER JOIN books b on invited.id = b.user_id
     * GROUP BY invited.invited_by_user_id
     * ORDER BY rating DESC;
     *
     * **************** Attention ****************
     * Do not try to use Join::ON, in Doctrine JOINS - it's doesnt work properly - use Join::WITH
     * https://github.com/doctrine/orm/issues/7193
     * *******************************************
     */
    public function getInvitersTop(): array
    {

       return $this->createQueryBuilder('u')
           ->select('u.name')
           ->addSelect('( SELECT count(invited_users.id)
             FROM App\Entity\Users invited_users
               WHERE invited_users.invited_by_user_id = u.id
           ) as icount')
           ->addSelect('COUNT(b.id) as bcount', 'SUM(b.rating) as rating')
           ->innerJoin(
               'App\Entity\Users',
               'invited',
               Join::WITH,
               'u.id = invited.invited_by_user_id'
           )
           ->innerJoin(
               'App\Entity\Books',
               'b',
               Join::WITH,
               'invited.id = b.user_id'
           )
           ->groupBy('invited.invited_by_user_id')
           ->orderBy('rating', 'DESC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
        ;
    }

}

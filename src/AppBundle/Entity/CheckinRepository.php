<?php
/**
 * Checkin Entity Repository
 * author: Haiping Lu
 */
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use AppBundle\Entity\User;

class CheckinRepository extends EntityRepository
{
    public function getCheckinsByUser(User $user)
    {
        $qb = $this->_em
                    ->createQueryBuilder()
                    ->select('a')
                    ->from($this->_entityName, 'a')
                    ->join('a.user', 'u')
                    ->join('u.myFriends', 'mf', 'with', 'mf.id = :userId')
                    ->setParameter('userId', $user->getId())
                    ->groupBy('a.id')
                    ->orderBy('a.created', 'DESC')
                    ->getQuery()->getSQL();var_dump($qb);die;
                //    ->getResult();
        return $qb;

    }
}
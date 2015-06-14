<?php
/**
 * Activity Entity Repository
 * author: Haiping Lu
 */
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ActivityRepository extends EntityRepository
{
    public function getActivitiesByUser(User $user)
    {
        $qb = $this->_em
                    ->createQueryBuilder()
                    ->select('a')
                    ->from($this->_entityName, 'a')
                    ->join('a.user', 'u')
                    ->leftJoin('u.myFriends', 'mf')
                    ->leftJoin('u.friendsWithMe', 'fm')
                    ->where('a.parentId IS NULL')
                    ->andWhere('mf.id = :userId OR fm.id = :userId')
                    ->setParameter('userId', $user->getId())
                    ->groupBy('a.id')
                    ->orderBy('a.created', 'DESC')
                    ->getQuery()
                    ->getResult();
        return $qb;

    }

}

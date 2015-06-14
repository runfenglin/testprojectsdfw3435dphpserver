<?php
/**
 * Trip Entity Repository
 * author: Haiping Lu
 */
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity as Entity;

class TripRepository extends EntityRepository
{

    public function getRideRequestsByUser(Entity\User $user)
    {
        $group = FALSE;
        
        return $this->_em
                    ->createQueryBuilder()
                    ->select('t')
                    ->from($this->_entityName, 't')
                    ->where('t.user = :User')
                    ->andWhere('t.group = :Group')
                    ->andWhere('t.parent IS NULL')
                    ->andWhere('t.driver IS NULL')
                    ->setParameter('Group', $group)
                    ->setParameter('User', $user)
                    ->getQuery()
                    ->getResult();
    }
    
    public function getGroupTripsByUser(Entity\User $user)
    {
        $group = TRUE;
        
        return $this->_em
                    ->createQueryBuilder()
                    ->select('t')
                    ->from($this->_entityName, 't')
                    ->where('t.user = :User')
                    ->andWhere('t.group = :Group')
                    ->setParameter('Group', $group)
                    ->setParameter('User', $user)
                    ->getQuery()
                    ->getResult();      
    }
    
    public function getTripsByUser(Entity\User $user)
    {
        $group = FALSE;
        
        return $this->_em
                    ->createQueryBuilder()
                    ->select('t')
                    ->from($this->_entityName, 't')
                    ->where('t.user = :User')
                    ->andWhere('t.driver IS NOT NULL')
                    ->andWhere('t.parent IS NULL')
                    ->andWhere('t.group = :Group')
                    ->setParameter('Group', $group)
                    ->setParameter('User', $user)
                    ->getQuery()
                    ->getResult();      
    }
    
    public function getFriendRequestsByUser(Entity\User $user)
    {
        $group = FALSE;
        
        $ffriendReqs = $this->_em
                           ->createQueryBuilder()
                           ->select('t')
                           ->from($this->_entityName, 't')
                           ->join('t.user', 'u')
                           ->leftJoin('u.myFriends', 'mf')
                           ->leftJoin('u.friendsWithMe', 'fm')
                           ->leftJoin('mf.myFriends', 'mfmf')
                           ->leftJoin('mf.friendsWithMe', 'fmmf')
                           ->leftJoin('fm.myFriends', 'mffm')
                           ->leftJoin('fm.friendsWithMe', 'fmfm')
                           ->where('t.driver IS NULL')
                           ->andWhere('t.parent IS NULL')
                           ->andWhere('(mf = :User OR fm = :User) OR ((mfmf = :User OR fmmf = :User OR mffm = :User OR fmfm = :User) AND t.visibility = ' . Trip::CIRCLE_FRIEND_OF_FRIEND . ') OR t.visibility = ' . Trip::CIRCLE_PUBLIC)               
                           ->andWhere('t.group = :Group')
                           ->setParameter('Group', $group)
                           ->setParameter('User', $user)
                           ->orderBy('t.created', 'DESC')
                           ->getQuery()//->getSQL();var_dump($friendReqs);die;
                           ->getResult();
        return $ffriendReqs;
    }
    
    public function getFriendGroupTripsByUser(Entity\User $user)
    {
        $group = TRUE;
        
        $groupTrips = $this->_em
                           ->createQueryBuilder()
                           ->select('t')
                           ->from($this->_entityName, 't')
                           ->join('t.user', 'u')
                           ->leftJoin('u.myFriends', 'mf')
                           ->leftJoin('u.friendsWithMe', 'fm')
                           ->leftJoin('mf.myFriends', 'mfmf')
                           ->leftJoin('mf.friendsWithMe', 'fmmf')
                           ->leftJoin('fm.myFriends', 'mffm')
                           ->leftJoin('fm.friendsWithMe', 'fmfm')
                           ->where('t.group = :Group')
                           ->andWhere('(mf = :User OR fm = :User) OR ((mfmf = :User OR fmmf = :User OR mffm = :User OR fmfm = :User) AND t.visibility = ' . Trip::CIRCLE_FRIEND_OF_FRIEND . ') OR t.visibility = ' . Trip::CIRCLE_PUBLIC)
                           ->setParameter('Group', $group)
                           ->setParameter('User', $user)
                           ->orderBy('t.created', 'DESC')
                           ->getQuery()
                           ->getResult();
        return $groupTrips;
    }
    
    public function getJoinedGroupTrips(Entity\User $user)
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('t')
                    ->from($this->_entityName, 't')
                    ->join('t.groupUsers', 'gu')
                    ->where('gu.user = :User')
                    ->setParameter('User', $user)
                    ->getQuery()
                    ->getResult();
    }
}

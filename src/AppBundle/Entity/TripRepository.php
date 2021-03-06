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
	public function getTripRequestSummary()
	{
		return $this->_em
                    ->createQueryBuilder()
                    ->select('COUNT(t.id) as num, DATE(t.created) AS date_group')
                    ->from($this->_entityName, 't')
					->where('t.group = FALSE')
					->where('t.driver IS NULL')
					->orderBy('t.created', 'DESC')
					->groupBy('date_group')
					->setMaxResults(10)
					->getQuery()
					->getScalarResult();
	}
	
	public function getRequestTotalCount()
	{
		return $this->_em
                    ->createQueryBuilder()
                    ->select('COUNT(t.id)')
                    ->from($this->_entityName, 't')
					->where('t.group = FALSE')
					->where('t.driver IS NULL')
					->getQuery()
					->getSingleScalarResult();
	}
	
	public function getTripSummary()
	{
		return $this->_em
                    ->createQueryBuilder()
                    ->select('COUNT(t.id) as num, DATE(t.created) AS date_group')
                    ->from($this->_entityName, 't')
					->where('t.group = FALSE')
					->where('t.driver IS NOT NULL')
					->orderBy('t.created', 'DESC')
					->groupBy('date_group')
					->setMaxResults(10)
					->getQuery()
					->getScalarResult();
	}
	
	public function getTripTotalCount()
	{
		return $this->_em
                    ->createQueryBuilder()
                    ->select('COUNT(t.id)')
                    ->from($this->_entityName, 't')
					->where('t.group = FALSE')
					->where('t.driver IS NOT NULL')
					->getQuery()
					->getSingleScalarResult();
	}

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
	
	public function getTripRequests()
	{
		//TODO implement pagination
		return $this->_em
                    ->createQueryBuilder()
                    ->select('t', 'u', 'o')
                    ->from($this->_entityName, 't')
					->join('t.user', 'u')
					->leftJoin('t.rideOffers', 'o')
                    ->where('t.driver IS NULL')
                    ->andWhere('t.group = :group')
					->setParameter('group', FALSE)
                    ->getQuery()
                    ->getResult();
	}
	
	public function getPairedTrips()
	{
		//TODO implement pagination
		return $this->_em
                    ->createQueryBuilder()
                    ->select('t', 'u', 'd', 'p')
                    ->from($this->_entityName, 't')
					->join('t.user', 'u')
					->join('t.driver', 'd')
					->leftJoin('t.parent', 'p')
                    ->getQuery()
                    ->getResult();
	}
	
	public function getGroupTrips()
	{
		//TODO implement pagination
		return $this->_em
                    ->createQueryBuilder()
                    ->select('t', 'u', 'g', 'gu')
                    ->from($this->_entityName, 't')
					->join('t.user', 'u')
					->leftJoin('t.groupUsers', 'g')
					->join('g.user', 'gu')
                    ->where('t.group = :group')
					->setParameter('group', TRUE)
                    ->getQuery()
                    ->getResult();
	}
	
	public function getGroupTripDetail($id)
	{
		return $this->_em
                    ->createQueryBuilder()
                    ->select('t', 'u', 'g', 'gu')
                    ->from($this->_entityName, 't')
					->join('t.user', 'u')
					->leftJoin('t.groupUsers', 'g')
					->join('g.user', 'gu')
                    ->where('t.id = :Id')
					->setParameter('Id', $id)
                    ->getQuery()
                    ->getResult();
	}
}

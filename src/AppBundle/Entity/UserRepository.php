<?php
/**
 * User Entity Repository
 * author: Haiping Lu
 */
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserRepository extends EntityRepository implements UserProviderInterface
{

    public function getUserByApiKey($apiKey)
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('u')
                    ->from($this->_entityName, 'u')
                    ->join('u.token', 't')
                    ->where('t.key = :key')
                    ->setParameter('key', $apiKey)
                    ->getQuery()
                    ->getOneOrNullResult();
    }
    
    public function getUserByPhoneLogin($phone, $country)
    {
        return $this->createQueryBuilder('u')
                    ->where('u.phone = :phone')
                    ->andWhere('u.country = :country')
                    ->setParameter('phone', $phone)
                    ->setParameter('country', $country)
                    ->getQuery()
                    ->getOneOrNullResult();
    }
    
    public function fbAutoLinkUsers(array $friendIds)
    {
        $type = SocialType::FACEBOOK;
        
        return $this->createQueryBuilder('u')
                    ->select('u')
                    ->join('u.socialAccounts', 'sa')
                    ->join('sa.type', 'st')
                    ->where('st.code = :socialType')
                    ->andWhere('sa.smId IN (:friendIds)')
                    ->setParameter('socialType', $type)
                    ->setParameter('friendIds', $friendIds)
                    ->getQuery()
                    ->getResult();
    }
    
    public function loadUserByUsername($username)
    {
        $user = $this->createQueryBuilder('u')
            ->where('u.phone = :phone OR u.email = :email')
            ->setParameter('phone', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();

        if (null === $user) {
            $message = sprintf(
                'Unable to find an active admin AppBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class
            || is_subclass_of($class, $this->getEntityName());
    }
    
    public function getUserFriends(User $user)
    {
        return $this->_em
               ->createQueryBuilder()
               ->select('u')
               ->from($this->_entityName, 'u')
               ->leftJoin('u.myFriends', 'mf')
               ->leftJoin('u.friendsWithMe', 'fm')
               ->where('mf = :User OR fm = :User')               
               ->setParameter('User', $user)
               ->getQuery()
               ->getResult();
    }
    
    public function getUserFriendsOfFriends(User $user)
    {
        return $this->_em
               ->createQueryBuilder()
               ->select('u')
               ->from($this->_entityName, 'u')
               ->leftJoin('u.myFriends', 'mf')
               ->leftJoin('u.friendsWithMe', 'fm')
               ->leftJoin('mf.myFriends', 'mfmf')
               ->leftJoin('mf.friendsWithMe', 'fmmf')
               ->leftJoin('fm.myFriends', 'mffm')
               ->leftJoin('fm.friendsWithMe', 'fmfm')
               ->where('mf = :User OR fm = :User OR mfmf = :User OR fmmf = :User OR mffm = :User OR fmfm = :User') 
               ->andWhere('u != :User')            
               ->setParameter('User', $user)
               ->getQuery()
               ->getResult();
    }
	
	public function getMediaAccounts()
	{
		return $this->_em
               ->createQueryBuilder()
               ->select('u', 'mf', 'fm', 'sm', 'st')
               ->from($this->_entityName, 'u')
               ->leftJoin('u.myFriends', 'mf')
               ->leftJoin('u.friendsWithMe', 'fm')
			   ->leftJoin('u.socialAccounts', 'sm')
			   ->leftJoin('sm.type', 'st')
               ->where('u.email IS NOT NULL') 
               ->getQuery()
               ->getResult();
	}
	
	public function getMobileAccounts()
	{
		return $this->_em
               ->createQueryBuilder()
               ->select('u', 'mf', 'fm', 'sm', 'st')
               ->from($this->_entityName, 'u')
               ->leftJoin('u.myFriends', 'mf')
               ->leftJoin('u.friendsWithMe', 'fm')
			   ->leftJoin('u.socialAccounts', 'sm')
			   ->leftJoin('sm.type', 'st')
               ->where('u.phone IS NOT NULL') 
               ->getQuery()
               ->getResult();
	}

}

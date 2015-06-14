<?php
/**
 * Comment Entity Repository
 * author: Haiping Lu
 */
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

use AppBundle\Entity\User;

class CommentRepository extends EntityRepository
{
    public function getUpdatedComment(User $user)
    {
        $qb = $this->_em
                   ->createQueryBuilder()
                   ->select('c')
                   ->from($this->_entityName, 'c')
                   ->join('c.parent', 'cp');

        if($user->getUpdateAt() instanceof \DateTime) {
            $qb->where('c.created > :datetime')
               ->setParameter('datetime', $user->getUpdateAt())
               ->andWhere('c.toUser = :toUser OR cp.user = :user');
        }
        else {
            $qb->where('c.toUser = :toUser OR cp.user = :user');
        }
        return $qb->setParameter('toUser', $user)
                  ->setParameter('user', $user)
                  ->getQuery()
                  ->getResult();
            
    }
}

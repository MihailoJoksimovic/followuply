<?php
/**
 * @author Mihailo Joksimovic <tinzey@gmail.com>
 */

namespace Followuply\Entity;

use Doctrine\ORM\EntityRepository;

/**
 *
 * @author Mihailo Joksimovic <tinzey@gmail.com>
 */
class UserRepository extends EntityRepository
{
    public function findByUsername($username)
    {
        return $this->findOneBy(array(
            'email' => $username
        ));
    }

    public function save(User $user)
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}


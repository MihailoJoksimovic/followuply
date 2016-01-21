<?php
/**
 * @author Mihailo Joksimovic <tinzey@gmail.com>
 */

namespace Followuply\Security;
use Followuply\Entity\User;


/**
 *
 * @author Mihailo Joksimovic <tinzey@gmail.com>
 */
interface UserRegistrationServiceInterface
{
    /**
     * @param User $user
     * @return bool
     * @throws UserAlreadyExistsException
     */
    public function register(User $user);
}


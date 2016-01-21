<?php
/**
 * @author Mihailo Joksimovic <tinzey@gmail.com>
 */

namespace Followuply\Security;
use Doctrine\ORM\EntityManager;
use Followuply\Entity\User;
use Followuply\Entity\UserRepository;
use Monolog\Logger;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 *
 * @author Mihailo Joksimovic <tinzey@gmail.com>
 */
class UserRegistrationService implements UserRegistrationServiceInterface
{
    /**
     * @var \Followuply\Entity\UserRepository
     */
    private $userRepository;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var \Monolog\Logger
     */
    private $logger;

    public function __construct(
        UserRepository $userRepository,
        EncoderFactoryInterface $encoderFactory,
        Logger $logger
    ) {
        $this->userRepository = $userRepository;
        $this->encoderFactory = $encoderFactory;
        $this->logger = $logger;
    }

    /**
     * @param User $user
     * @return bool
     * @throws UserAlreadyExistsException
     */
    public function register(User $user)
    {
        $logger = $this->logger;

        $logger->info(sprintf("Handling new registration - Username: %s", $user->getUsername()));

        $existingUser = $this->userRepository->findByUsername($user->getUsername());

        if ($existingUser instanceof User) {
            throw new UserAlreadyExistsException(sprintf("User with username %s already exists!", $user->getUsername()));
        }

        $encoder = $this->encoderFactory->getEncoder($user);

        $encodedPassword = $encoder->encodePassword($user->getPassword(), $user->getSalt());

        $user->setPassword($encodedPassword);

        $this->userRepository->save($user);

        $logger->info("User stored to DB successfully.");
    }

}



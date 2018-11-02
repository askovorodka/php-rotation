<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends CoreFixtures
{
    // На данный момент у юзера может быть только две роли.
    public const USER_DEFAULT_ROLES = [
        'ROLE_ADMIN',
        'ROLE_MANAGER'
    ];

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function load(ObjectManager $manager): void
    {
        parent::load($manager);
        $this->createUser('testUser', 'p@ssword', 'test@test.ru');
        $this->getManager()->flush();
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @return User
     */
    public function createUser(string $username, string $password, string $email): User
    {
        $user = new User();
        $user
            ->setUsername($username)
            ->setPassword($password)
            ->addRole(self::USER_DEFAULT_ROLES[0])
            ->setEmail($email);
        $this->getManager()->persist($user);

        return $user;
    }
}
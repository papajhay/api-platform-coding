<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const USER_REFERENCE_PREFIX = 'user_';

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            ['email' => 'user1@example.com', 'roles' => ['ROLE_USER']],
            ['email' => 'user2@example.com', 'roles' => ['ROLE_USER']],
            ['email' => 'user3@example.com', 'roles' => ['ROLE_USER']],
            ['email' => 'user4@example.com', 'roles' => ['ROLE_ADMIN']],
            ['email' => 'user5@example.com', 'roles' => ['ROLE_SUPER_ADMIN']],
        ];

        foreach ($users as $index => $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles($data['roles']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE_PREFIX . ($index + 1), $user);
        }

        $manager->flush();
    }
}

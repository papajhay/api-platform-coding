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
        $faker = \Faker\Factory::create();

        $usersData = [
            ['roles' => ['ROLE_USER']],
            ['roles' => ['ROLE_USER']],
            ['roles' => ['ROLE_USER']],
            ['roles' => ['ROLE_ADMIN']],
            ['roles' => ['ROLE_SUPER_ADMIN']],
        ];

        foreach ($usersData as $index => $data) {
            $user = new User();

            // Email généré par Faker (unique)
            $email = $faker->unique()->safeEmail();
            $user->setEmail($email);

            // Roles dynamiques pris depuis $usersData
            $roles = $data['roles'] ?? ['ROLE_USER'];
            $user->setRoles($roles);

            // Mot de passe "password" hashé
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));

            $manager->persist($user);

            // Ajout d'une référence pour lier avec d'autres fixtures (user_1..user_5)
            $this->addReference(self::USER_REFERENCE_PREFIX . ($index + 1), $user);
        }

        $manager->flush();
    }
}

<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public const POST_REFERENCE_PREFIX = 'post_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 20; ++$i) {

            $post = new Post();

            $createdAt = \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-12 months', 'now')
            );

            /** @var User $author */
            $author = $this->getReference(
                UserFixtures::USER_REFERENCE_PREFIX . (($i - 1) % 5 + 1),
                User::class
            );

            $post
                ->setTitle($faker->sentence(6))
                ->setContent($faker->paragraphs(3, true))
                ->setSlug(sprintf('post-%d', $i))
                ->setAuthor($author)
                ->setCreatedAt($createdAt)
                ->setUpdatedAt($createdAt);

            $manager->persist($post);

            // IMPORTANT : ajouter la référence
            $this->addReference(
                self::POST_REFERENCE_PREFIX . $i,
                $post
            );
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
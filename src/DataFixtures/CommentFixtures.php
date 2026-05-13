<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct()
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Default number of comments to create. Adjust this value to configure fixture volume.
        $commentsCount = 60;

        // Expected existing references:
        // - users: 'user_1' .. 'user_5'
        // - posts: 'post_1' .. 'post_20'
        for ($i = 1; $i <= $commentsCount; ++$i) {
            /** @var Post $post */
            $post = $this->getReference('post_' . (($i - 1) % 20 + 1), Post::class);

            /** @var User $author */
            $author = $this->getReference('user_' . (($i - 1) % 5 + 1), User::class);

            $postCreatedAt = $post->getCreatedAt();
            $createdAt = \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween($postCreatedAt->format('Y-m-d H:i:s'), 'now')
            );

            $comment = new Comment();
            $comment
                ->setContent($faker->words($faker->numberBetween(10, 40), true))
                ->setCreatedAt($createdAt)
                ->setPost($post)
                ->setAuthor($author);

            // Optional nullable updatedAt support when the entity exposes a setter.
            if (method_exists($comment, 'setUpdatedAt')) {
                $updatedAt = $faker->boolean(35)
                    ? null
                    : \DateTimeImmutable::createFromMutable(
                        $faker->dateTimeBetween($createdAt->format('Y-m-d H:i:s'), 'now')
                    );

                $comment->setUpdatedAt($updatedAt);
            }

            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class, PostFixtures::class];
    }
}

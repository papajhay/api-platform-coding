<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
final class UploadPostImageAction
{
    public function __invoke(Request $request, Security $security, EntityManagerInterface $entityManager): Response
    {
        $title = (string) $request->request->get('title', '');
        $content = (string) $request->request->get('content', '');

        if ('' === trim($title) || '' === trim($content)) {
            throw new BadRequestHttpException('Missing required multipart fields "title" and "content".');
        }

        $uploadedFile = $request->files->get('imageFile');
        if (!$uploadedFile instanceof UploadedFile) {
            $uploadedFile = $request->files->get('file');
        }

        if (!$uploadedFile instanceof UploadedFile) {
            throw new BadRequestHttpException('Missing required multipart field "imageFile".');
        }

        $author = $security->getUser();
        if (!$author instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user required to create a post.');
        }

        $post = new Post();
        $post->setTitle($title);
        $post->setContent($content);
        $post->setAuthor($author);
        $post->setImageFile($uploadedFile);

        $entityManager->persist($post);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'imageName' => $post->getImageName(),
            'imageUrl' => $post->getImageUrl(),
            'author' => [
                'id' => $author->getId(),
                'email' => $author->getEmail(),
            ],
        ], Response::HTTP_CREATED);
    }
}

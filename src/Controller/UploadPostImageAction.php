<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
final class UploadPostImageAction
{
    public function __invoke(Request $request, Post $post): Post
    {
        $uploadedFile = $request->files->get('file');

        if (!$uploadedFile) {
            throw new BadRequestHttpException('Missing required multipart field "file".');
        }

        $post->setImageFile($uploadedFile);

        return $post;
    }
}

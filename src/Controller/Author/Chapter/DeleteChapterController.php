<?php

declare(strict_types=1);

namespace App\Controller\Author\Chapter;

use App\Controller\BaseController;
use App\Entity\Chapter;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_USER)]
#[Route(
    path: '/author/book/{book_uuid}/chapter/delete/{chapter_uuid}',
    name: 'app_author_chapter_delete',
    requirements: ['book_uuid' => self::UUID_REGEX, 'chapter_uuid' => self::UUID_REGEX],
    methods: Request::METHOD_POST
)]
final class DeleteChapterController extends BaseController
{
    public function __invoke(string $book_uuid, string $chapter_uuid, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (false === $this->isCsrfTokenValid('delete'.$chapter_uuid, (string) $request->request->get('csrf_token'))) {
            return $this->redirectToRoute('app_chapter_index');
        }

        $chapter = $entityManager->getRepository(Chapter::class)->find($chapter_uuid);

        if (null === $chapter) {
            throw $this->createNotFoundException();
        }

        $entityManager->remove($chapter);
        $entityManager->flush();

        return $this->redirectToRoute('app_author_chapter_index', ['book_uuid' => $book_uuid]);
    }
}

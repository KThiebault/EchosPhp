<?php

declare(strict_types=1);

namespace App\Controller\Admin\Tag;

use App\Controller\BaseController;
use App\Entity\Tag;
use App\Entity\User;
use App\Form\TagType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_ADMIN)]
#[Route(
    'admin/tag/update/{uuid}',
    name: 'app_admin_tag_update',
    requirements: ['uuid' => self::UUID_REGEX],
    methods: [Request::METHOD_GET, Request::METHOD_POST]
)]
final class UpdateTagController extends BaseController
{
    public function __invoke(string $uuid, Request $request, EntityManagerInterface $entityManager): Response
    {
        $tag = $entityManager->getRepository(Tag::class)->find($uuid);

        if (null === $tag) {
            throw $this->createNotFoundException();
        }

        $tagForm = $this->createForm(TagType::class, $tag)->handleRequest($request);

        if ($tagForm->isSubmitted() && $tagForm->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_tag_update', ['uuid' => $uuid]);
        }

        return $this->render('admin/tag/update.html.twig', ['tag_form' => $tagForm]);
    }
}
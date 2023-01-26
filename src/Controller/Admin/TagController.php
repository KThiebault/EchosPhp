<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Tag;
use App\Form\TagType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
final class TagController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/tag', name: 'app_admin_tag_index', methods: Request::METHOD_GET)]
    public function index(): Response
    {
        return $this->render(
            'admin/tag/index.html.twig',
            ['tags' =>  $this->entityManager->getRepository(Tag::class)->findAll()]
        );
    }

    #[Route(path: '/tag/create', name: 'app_admin_tag_create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function create(Request $request): Response
    {
        $tag = new Tag();
        $tagForm = $this->createForm(TagType::class, $tag)->handleRequest($request);

        if ($tagForm->isSubmitted() && $tagForm->isValid()) {
            $this->entityManager->persist($tag);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_admin_tag_index');
        }

        return $this->render('admin/tag/create.html.twig', ['tag_form' => $tagForm]);
    }

    #[Route(
        '/tag/update/{uuid}',
        name: 'app_admin_tag_update',
        requirements: ['uuid' => '^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$'],
        methods: [Request::METHOD_GET, Request::METHOD_POST]
    )]
    public function update(string $uuid, Request $request): Response
    {
        $tag = $this->entityManager->getRepository(Tag::class)->find($uuid);

        if (null === $tag) {
            throw $this->createNotFoundException();
        }

        $tagForm = $this->createForm(TagType::class, $tag)->handleRequest($request);

        if ($tagForm->isSubmitted() && $tagForm->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_admin_tag_update', ['uuid' => $uuid]);
        }

        return $this->render('admin/tag/update.html.twig', ['tag_form' => $tagForm]);
    }
}

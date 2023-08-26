<?php

declare(strict_types=1);

namespace App\Controller\Admin\Tag;

use App\Entity\Tag;
use App\Entity\User;
use App\Form\TagType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_ADMIN)]
#[Route(path: 'admin/tag/create', name: 'app_admin_tag_create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
final class CreateTagController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tag = new Tag();
        $tagForm = $this->createForm(TagType::class, $tag)->handleRequest($request);

        if ($tagForm->isSubmitted() && $tagForm->isValid()) {
            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_tag_index');
        }

        return $this->render('admin/tag/create.html.twig', ['tag_form' => $tagForm]);
    }
}
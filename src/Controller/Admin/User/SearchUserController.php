<?php

declare(strict_types=1);

namespace App\Controller\Admin\User;

use App\Entity\User;
use App\Form\SearchUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_ADMIN)]
#[Route('admin/user/search', name: 'app_admin_user_search', methods: [Request::METHOD_GET, Request::METHOD_POST])]
final class SearchUserController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchForm = $this->createForm(SearchUserType::class)->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $user = $entityManager->getRepository(User::class)->findOneBy(['pseudo' => $searchForm->get('pseudo')->getData()]);

            if (null !== $user) {
                return $this->redirectToRoute('app_admin_user_show', ['uuid' => $user->getUuid()]);
            }

            $searchForm->get('pseudo')->addError(new FormError('User not found'));
        }

        return $this->render('admin/user/search.html.twig', ['search_form' => $searchForm]);
    }
}
<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\History;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/user/history', name: 'app_user_history', methods: Request::METHOD_GET)]
    public function history(EntityManagerInterface $entityManager): Response
    {
        return $this->render('user/history.html.twig', [
            'histories' => $entityManager->getRepository(History::class)->findBy(['user' => $this->getUser()]),
        ]);
    }
}

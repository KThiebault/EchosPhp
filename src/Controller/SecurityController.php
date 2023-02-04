<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

final class SecurityController extends AbstractController
{
    #[Route('/registration', name: 'app_security_registration', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function registration(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $registrationForm = $this->createForm(RegistrationType::class)->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            /** @var User $user */
            $user = $registrationForm->getData();
            /** @var string $plainPassword */
            $plainPassword = $registrationForm->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_security_login');
        }

        return $this->render('security/registration.html.twig', ['registration_form' => $registrationForm]);
    }

    #[Route('/logout', name: 'app_security_logout', methods: Request::METHOD_GET)]
    public function logout(): Response
    {
        throw new \Exception('This should never be reached!');
    }
}

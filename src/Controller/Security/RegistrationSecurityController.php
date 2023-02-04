<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Controller\BaseController;
use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/registration', name: 'app_security_registration', methods: [Request::METHOD_GET, Request::METHOD_POST])]
final class RegistrationSecurityController extends BaseController
{
    public function __invoke(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $registrationForm = $this->createForm(RegistrationType::class)->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            /** @var User $user */
            $user = $registrationForm->getData();
            /** @var string $plainPassword */
            $plainPassword = $registrationForm->get('plainPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_security_login');
        }

        return $this->render('security/registration.html.twig', ['registration_form' => $registrationForm]);
    }
}

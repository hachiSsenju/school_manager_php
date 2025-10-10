<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
   #[Route('/register', name: 'app_register', methods: ['POST'])]
public function register(
    Request $request,
    UserPasswordHasherInterface $passwordHasher,
    EntityManagerInterface $entityManager
): Response {
    $data = json_decode($request->getContent(), true);

    if (!$data || !isset($data['email'],$data['telephone'], $data['password'], $data['nom'], $data['prenom'])) {
        return $this->json(['error' => 'Invalid payload'], 400);
    }

    $user = new User();
    $user->setEmail($data['email']);
    $user->setNom($data['nom']);
    $user->setPrenom($data['prenom']);
    $user->setTelephone($data['telephone']);
    $user->setRoles([$data['role_user'] ?? 'utilisateur']);
    $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

    $entityManager->persist($user);
    $entityManager->flush();

    return $this->json([
        'success' => true,
        'user' => [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getNom(),
            "telephone" => $user->getTelephone(),
            'prename' => $user->getPrenom(),
            'roles' => $user->getRoles(),
        ]
    ], 201);
}

}

<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/api/users', name: 'app_user', methods: ['GET'])]
    public function index(UserRepository $userRepo): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access denied.'], 403);
        }

        $users = $userRepo->findAll();

        // Convert User objects to arrays
        $data = array_map(function ($user) {
            return [
                'id'      => $user->getId(),
                'email'   => $user->getEmail(),
                'name'    => $user->getNom(),
                'prename' => $user->getPrenom(),
                'roles'   => $user->getRoles(),
                'telephone' => $user->getTelephone(),
            ];
        }, $users);

        return $this->json($data);
    }
    #[Route('/user/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(int $id, UserRepository $userRepo): Response
    {

        $user = $userRepo->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found.'], 404);
        }

        $data = [
            'id'      => $user->getId(),
            'email'   => $user->getEmail(),
            'name'    => $user->getNom(),
            'prename' => $user->getPrenom(),
            'roles'   => $user->getRoles(),
            'telephone' => $user->getTelephone(),
        ];

        return $this->json($data);
    }
    #[Route('/api/user/edit/{id}', name: 'app_user_show', methods: ['PUT'])]
    public function edit(int $id, UserRepository $userRepo, Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = $userRepo->find($id);

        if (!$user) {
            return $this->json(['error' => 'User not found.'], 404);
        }
        $user->setEmail($data['email'] ?? $user->getEmail());
        $user->setNom($data['nom'] ?? $user->getNom());
        $user->setPrenom($data['prenom'] ?? $user->getPrenom());
        $user->setTelephone($data['telephone'] ?? $user->getTelephone());
        $user->setRoles([$data['role_user'] ?? $user->getRoles()[0] ?? 'utilisateur']);
        $em->flush();
        $data = [
            'id'      => $user->getId(),
            'email'   => $user->getEmail(),
            'name'    => $user->getNom(),
            'prename' => $user->getPrenom(),
            'roles'   => $user->getRoles(),
            'telephone' => $user->getTelephone(),
        ];

        return $this->json($data);
    }
    #[Route('/api/user/delete/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(int $id, UserRepository $userRepo, EntityManagerInterface $em): Response
    {
        $user = $userRepo->find($id);
        if (!$user) {
            return $this->json(['error' => 'User not found.'], 404);
        }
        $em->remove($user);
        $em->flush();
        return $this->json(['success' => 'User deleted.']);
    }
}

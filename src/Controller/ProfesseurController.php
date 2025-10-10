<?php

namespace App\Controller;

use App\Entity\Professeur;
use App\Repository\EcoleRepository;
use App\Repository\ProfesseurRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfesseurController extends AbstractController
{
    #[Route('/api/professeurs', name: 'app_professeur', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['user_id'])) {
            return $this->json(['invalid payload', 404]);
        }
        $user_id = intval($data['user_id']);
        $utilisateur = $userRepository->find($user_id);
        if (!$utilisateur) {
            return $this->json(['user not found', 404]);
        }
        $profs = $utilisateur->getClasse()->getProfesseur();
        $data = array_map(function ($prof) {
            return [
                'id'      => $prof->getId(),
                'nom'    => $prof->getNom(),
                'prenom' => $prof->getPrenom(),
                'telephone' => $prof->getTelephone(),
            ];
        }, $profs);
        return $this->json($data);
    }
    #[Route('/api/admin/professeurs', name: 'admin_professeur', methods: ['GET'])]
    public function admin(ProfesseurRepository $professeurRepository): JsonResponse
    {
        $profs = $professeurRepository->findAll();
        $data = array_map(function ($prof) {
            return [
                'id'      => $prof->getId(),
                'nom'    => $prof->getNom(),
                'prenom' => $prof->getPrenom(),
                'telephone' => $prof->getTelephone(),
            ];
        }, $profs);
        return $this->json($data);
    }
    #[Route('/api/professeurs/{id}', name: 'app_professeur_show', methods: ['GET'])]
    public function show(int $id, ProfesseurRepository $professeurRepository): JsonResponse
    {
        $prof = $professeurRepository->find($id);

        if (!$prof) {
            return $this->json(['error' => 'Professeur not found.'], 404);
        }

        $data = [
            'id' => $prof->getId(),
            'nom'    => $prof->getNom(),
            'prenom' => $prof->getPrenom(),
            'telephone' => $prof->getTelephone(),
        ];

        return $this->json($data);
    }
    #[Route('/api/professeurs', name: 'app_professeur_add', methods: ['POST'])]
    public function add(EntityManagerInterface $em, Request $request, EcoleRepository $ecoleRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (
            !$data ||
            !isset($data['telephone'], $data['nom'], $data['prenom'],$data['ecole_id'])
        ) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }
        $ecole_id = intval($data['ecole_id']);
        $ecole = $ecoleRepository->find($ecole_id);
        if (!$ecole) {
            return $this->json(['ecole not found', 404]);
        }
        $prof = new Professeur();
        $prof->setNom($data['nom']);
        $prof->setPrenom($data['prenom']);
        $prof->setTelephone($data['telephone']);
        $prof->setEcole($ecole);
        $em->persist($prof);
        $em->flush();
        $data = [
            "ecole_id" => $ecole->getId(),
            "user_id"=> $ecole->getUtilisateur()->getId(),
            'status' => 201,
            'id' => $prof->getId(),
            'nom'    => $prof->getNom(),
            'prenom' => $prof->getPrenom(),
            'telephone' => $prof->getTelephone(),
        ];

        return $this->json($data);
    }
    #[Route('/api/professeurs/edit/{id}', name: 'app_professeur_edit', methods: ['PUT'])]
    public function edit(int $id, ProfesseurRepository $professeurRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $prof = $professeurRepository->find($id);
        if (!$prof) {
            return $this->json(['error' => 'Professeur not found.'], 404);
        }
        $prof->setNom($data['nom'] ?? $prof->getNom());
        $prof->setPrenom($data['prenom'] ?? $prof->getPrenom());
        $prof->setTelephone($data['telephone'] ?? $prof->getTelephone());
        $em->flush();
        $datas = [
            'id' => $prof->getId(),
            'nom'    => $prof->getNom(),
            'prenom' => $prof->getPrenom(),
            'telephone' => $prof->getTelephone(),
        ];
        return $this->json($datas);
    }
}

<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Entity\Matiere;
use App\Entity\Professeur;
use App\Repository\MatiereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MatiereController extends AbstractController
{
    #[Route('/api/matieres', name: 'app_matiere', methods: ['GET'])]
    public function index(MatiereRepository $matiereRepository): JsonResponse
    {
        $matieres = $matiereRepository->findAll();
        $data = array_map(function ($matiere) {
            $professeur = $matiere->getProfesseur();
            $classe = $matiere->getClasse();
            return [
                'id'          => $matiere->getId(),
                'nom'         => $matiere->getNom(),
                'coefficient' => $matiere->getCoefficient(),
                'professeur'  => $professeur ? [
                    'id'   => $professeur->getId(),
                    'nom'  => $professeur->getNom(),
                    'prenom' => $professeur->getPrenom(),
                ] : null,
                'classe'  => $classe ? [
                    'id'   => $classe->getId(),
                    'nom'  => $classe->getNom(),
                    'niveau'  => $classe->getNiveau(),
                    'frais'  => $classe->getFrais(),
                    'nbMax'  => $classe->getNbMax(),
                ] : null,
            ];
        }, $matieres);
        return $this->json($data);
    }
    #[Route('/api/matieres', name: 'add_matiere', methods: ['POST'])]
    public function add(MatiereRepository $matiereRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (
            !$data ||
            !isset($data['coefficient'], $data['nom'], $data['professeur_id'],$data['classe_id'])
        ) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }
        $prof_id = intval($data['professeur_id']);
        $classe_id = intval($data['classe_id']);
        $prof = $em->getRepository(Professeur::class)->find($prof_id);
        $classe = $em->getRepository(Classe::class)->find($classe_id);
        if(!$prof || !$classe){
            return $this->json('prof or classe not found',404);
        }
        $matiere = new Matiere();
        $matiere->setNom($data['nom']);
        $matiere->setCoefficient($data['coefficient']);
        $matiere->setProfesseur($prof);
        $matiere->setClasse($classe);
        $em->persist($matiere);
        $em->flush();
        $data = [
            'id' => $matiere->getId(),
            'nom'    => $matiere->getNom(),
            'coefficient' => $matiere->getCoefficient(),
            'professeur_id' => $matiere->getProfesseur()->getId(),
            'classe_id' => $matiere->getClasse()->getId(),
            // 'professeur' => $matiere->getProfesseur(),
        ];
        return $this->json($data, 201);
    }
    #[Route('/api/matieres/{id}', name: 'app_matiere_id', methods: ['GET'])]
    public function show(MatiereRepository $matiereRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $id = $request->get('id');
        $matiere = $matiereRepository->find($id);
        if (!$matiere) {
            return $this->json(['error' => 'Matiere not found.'], 404);
        }
        $professeur = $matiere->getProfesseur();
        $data = [
            'id'          => $matiere->getId(),
            'nom'         => $matiere->getNom(),
            'coefficient' => $matiere->getCoefficient(),
            'professeur'  => $professeur ? [
                'id'   => $professeur->getId(),
                'nom'  => $professeur->getNom(),
                'prenom' => $professeur->getPrenom(),
            ] : null,
        ];
        return $this->json($data);
    }
    #[Route('/api/matieres/delete/{id}', name: 'delete_matiere_id', methods: ['DELETE'])]
    public function delete(MatiereRepository $matiereRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $id = $request->get('id');
        $matiere = $matiereRepository->find($id);
        if (!$matiere) {
            return $this->json(['error' => 'Matiere not found.'], 404);
        }
        $em->remove($matiere);
        $em->flush();
        return $this->json(['message' => 'Matiere deleted successfully.'], 200);
    }
    #[Route('/api/matieres/edit/{id}', name: 'app_matiere_edit', methods: ['PUT'])]
    public function edit(int $id ,MatiereRepository $matiereRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $prof_id = intval($data['professeur_id'] ?? null);
        $mat_id = intval($id);
        if ($prof_id) {
            $prof = $em->getRepository(Professeur::class)->find($prof_id);
        }
        $matiere = $matiereRepository->find($mat_id);
        $matiere->setNom($data['nom']);
        $matiere->setCoefficient($data['coefficient'] ?? $matiere->getCoefficient());
        $matiere->setProfesseur($prof ?? $matiere->getProfesseur());
        $em->flush();
        $data = [
            'id' => $matiere->getId(),
            'nom'    => $matiere->getNom(),
            'coefficient' => $matiere->getCoefficient(),
            'professeur_id' => $matiere->getProfesseur()->getId(),
        ];
        return $this->json($data, 202);
    }
}

<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Entity\Trimester;
use App\Repository\ClasseRepository;
use App\Repository\EcoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ClasseController extends AbstractController
{
    #[Route('/api/classes', name: 'app_classe', methods: ['GET'])]
    public function index(ClasseRepository $classeRepository): JsonResponse
    {
        $classes = $classeRepository->findAll();
        $data = array_map(function ($classe) {
            $matieres = $classe->getMatieres()->toArray();
            $eleves = $classe->getEleves()->toArray();
            $trimesters = $classe->getTrimesters()->toArray();
            return [
                'id'     => $classe->getId(),
                'nom'    => $classe->getNom(),
                'niveau' => $classe->getNiveau(),
                'frais'  => $classe->getFrais(),
                'nbMax'  => $classe->getNbMax(),
                'matieres' => array_map(function ($matiere) {
                    return [
                        'id' => $matiere->getId(),
                        'nom' => $matiere->getNom(),
                        'coefficient' => $matiere->getCoefficient(),
                    ];
                }, $matieres),
                'eleves' => array_map(function ($eleve) {
                    return [
                        'id' => $eleve->getId(),
                        'nom' => $eleve->getNom(),
                        'prenom' => $eleve->getPrenom(),
                        'birthday' => $eleve->getBirthday(),
                        'solde_initial' => $eleve->getSoldeInitial(),
                        'email_parent' => $eleve->getEmailParent(),
                        "bulletins" => array_map(function ($bulletin) {
                            return [
                                'id' => $bulletin->getId(),
                                'trimester' => $bulletin->getTrimester() ? $bulletin->getTrimester()->getLibelle() : null,
                                'grades' => array_map(function ($grade) {
                                    return [
                                        'id' => $grade->getId(),
                                        'note' => $grade->getNote(),
                                        'note_maximal' => $grade->getNoteMaximal(),
                                        'type_examen' => $grade->getTypeExamen(),
                                        'trimestre' => $grade->getTrimester(),
                                        'date' => $grade->getDate(),
                                        'matiere' => $grade->getMatiere() ? [
                                            'id' => $grade->getMatiere()->getId(),
                                            'nom' => $grade->getMatiere()->getNom(),
                                            'coef' => $grade->getMatiere()->getCoefficient(),
                                        ] : null,
                                    ];
                                }, $bulletin->getGrades()->toArray())
                            ];
                        }, $eleve->getBulletins()->toArray()),
                    ];
                }, $eleves),
                "trimesters" => array_map(function ($trimester) {
                    return [
                        'id' => $trimester->getId(),
                        'libelle' => $trimester->getLibelle(),
                    ];
                }, $trimesters)
            ];
        }, $classes);
        return $this->json($data);
    }
    #[Route('/api/classes/{id}', name: 'get_classe', methods: ['GET'])]
    public function show(ClasseRepository $classeRepository, int $id): JsonResponse
    {
        $class_id = intval($id);
        $classe = $classeRepository->find($class_id);
        if (!$classe) {
            return $this->json(['error' => 'Classe not found'], 404);
        }
        $classes = $classeRepository->find($class_id);
        $data =
            $matieres = $classe->getMatieres()->toArray();
        $data = [
            'id'     => $classe->getId(),
            'nom'    => $classe->getNom(),
            'niveau' => $classe->getNiveau(),
            'frais'  => $classe->getFrais(),
            'nbMax'  => $classe->getNbMax(),
            'matieres' => array_map(function ($matiere) {
                return [
                    'id' => $matiere->getId(),
                    'nom' => $matiere->getNom(),
                    'coefficient' => $matiere->getCoefficient(),
                ];
            }, $matieres),
        ];
        return $this->json($data);
    }
    #[Route('/api/classes', name: 'add_classe', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em, EcoleRepository $ecoleRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (
            !$data ||
            !isset($data['nom'], $data['niveau'], $data['frais'], $data['nbMax'], $data['ecole_id'],)
        ) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }
        $ecole_id = intval($data['ecole_id']);
        $ecole = $ecoleRepository->find($ecole_id);
        if (!$ecole) {
            return $this->json(['ecole not found', 404]);
        }
        $classe = new Classe();
        $classe->setNom($data['nom']);
        $classe->setNiveau($data['niveau']);
        $classe->setFrais($data['frais']);
        $classe->setNbMax($data['nbMax']);
        $classe->setEcole($ecole);
        if ($data['niveau'] === 'college' || $data['niveau'] === 'primaire' || $data['niveau'] === 'maternelle') {
            $trimester = new Trimester();
            $trimester->setClasse($classe);
            $trimester->setLibelle('Trimestre 1');
            $trimester2 = new Trimester();
            $trimester2->setClasse($classe);
            $trimester2->setLibelle('Trimestre 2');
            $trimester3 = new Trimester();
            $trimester3->setClasse($classe);
            $trimester3->setLibelle('Trimestre 3');
            $em->persist($trimester);
            $em->persist($trimester2);
            $em->persist($trimester3);
        }
        if ($data['niveau'] === 'lycee') {
            $trimester = new Trimester();
            $trimester->setClasse($classe);
            $trimester->setLibelle('Semestre 1');
            $trimester2 = new Trimester();
            $trimester2->setClasse($classe);
            $trimester2->setLibelle('Semestre 2');
            $em->persist($trimester);
            $em->persist($trimester2);
        }
        $em->persist($classe);
        $em->flush();
        $trimesters = $classe->getTrimesters()->toArray();
        $data = [
            'id'     => $classe->getId(),
            'nom'    => $classe->getNom(),
            'niveau' => $classe->getNiveau(),
            'frais'  => $classe->getFrais(),
            'nbMax'  => $classe->getNbMax(),
            'trimesters' => array_map(function ($trimester) {
                return [
                    'id' => $trimester->getId(),
                    'Libelle' => $trimester->getLibelle(),

                ];
            }, $trimesters),
        ];
        return $this->json($data);
    }

    #[Route('/api/classes/edit/{id}', name: 'edit_classe', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $em, int $id): JsonResponse
    {
        $classe = $em->getRepository(Classe::class)->find($id);
        if (!$classe) {
            return $this->json(['error' => 'Classe not found'], 404);
        }
        $data = json_decode($request->getContent(), true);
        $classe = new Classe();
        $classe->setNom($data['nom'] ?? $classe->getNom());
        $classe->setNiveau($data['niveau'] ?? $classe->getNiveau());
        $classe->setFrais($data['frais'] ?? $classe->getFrais());
        $classe->setNbMax($data['nbMax'] ?? $classe->getNbMax());
        $em->persist($classe);
        $em->flush();
        return $this->json([
            'message' => 'Classe edited successfully',
            'id' => $classe->getId(),
            'nom' => $classe->getNom(),
            'frais' => $classe->getFrais(),
            'niveau' => $classe->getNiveau(),
            'nbMax' => $classe->getNbMax(),
        ], 201);
    }
    #[Route('/api/classes/delete/{id}', name: 'edit_classe', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, int $id): JsonResponse
    {
        $classe_id = intval($id);
        $classe = $em->getRepository(Classe::class)->find($id);
        if (!$classe) {
            return $this->json(['error' => 'Classe not found'], 404);
        }
        $em->remove($classe);
        $em->flush();
        return $this->json(['message' => 'Classe deleted successfully'], 200);
    }

    #[Route('/api/classes/matieres/{id}', name: 'matieres_classes', methods: ['GET'])]
    public function matieresClasses(ClasseRepository $classeRepository, int $id): JsonResponse
    {
        $class_id = intval($id);
        $classe = $classeRepository->find($class_id);
        if (!$classe) {
            return $this->json(['error' => 'Classe not found'], 404);
        }
        $matieres = $classe->getMatieres()->toArray();
        $data = array_map(function ($matiere) {
            return [
                'id' => $matiere->getId(),
                'nom' => $matiere->getNom(),
                'coefficient' => $matiere->getCoefficient(),
                "professeur" => $matiere->getProfesseur() ? [
                    'id' => $matiere->getProfesseur()->getId(),
                    'nom' => $matiere->getProfesseur()->getNom(),
                    'prenom' => $matiere->getProfesseur()->getPrenom(),
                ] : null,
            ];
        }, $matieres);
        return $this->json($data);
    }
}

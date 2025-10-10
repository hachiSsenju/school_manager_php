<?php

namespace App\Controller;

use App\Entity\Ecole;
use App\Repository\EcoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EcoleController extends AbstractController
{
    #[Route('/api/ecoles/{id}', name: 'app_ecole', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request, int $id): Response
    {
        // $data = json_decode($request->getContent(), true);
        // if (!$data || !isset($data['user_id'])) {
        //     return $this->json(["invalid payload", 400]);
        // }
        $user_id = intval($id);
        $utilisateur = $userRepository->find($user_id);
        if (!$utilisateur) {
            return $this->json(["utilisateur introuvable", 404]);
        }

        $ecoles = $utilisateur->getEcole()->toArray();
        $data = array_map(function ($ecole) {
            $classes = $ecole->getClasse()->toArray();
            $eleves = $ecole->getEleves()->toArray();
            $profs = $ecole->getProfesseurs()->toArray();
            return [
                'id'     => $ecole->getId(),
                'nom'    => $ecole->getNom(),
                'classes' => array_map(function ($classe) {
                    $matieres = $classe->getMatieres()->toArray();
                    $eleves = $classe->getEleves()->toArray();
                    $trimesters = $classe->getTrimesters()->toArray();
                    return [
                        'id' => $classe->getId(),
                        'nom' => $classe->getNom(),
                        'niveau' => $classe->getNiveau(),
                        'frais' => $classe->getFrais(),
                        'nbMax' => $classe->getNbMax(),
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
                                "classe" => [
                                    "id" => $eleve->getClasse()->getId(),
                                    "nom" => $eleve->getClasse()->getNom(),
                                    "niveau" => $eleve->getClasse()->getNiveau(),
                                    "nbMax" => $eleve->getClasse()->getNbMax(),

                                ]
                            ];
                        }, $eleves),
                        "trimesters" => array_map(function ($trimester) {
                            return [
                                'id' => $trimester->getId(),
                                'libelle' => $trimester->getLibelle(),
                            ];
                        }, $trimesters)

                    ];
                }, $classes),
                'eleves' => array_map(function ($eleve) {
                    return [
                        'id' => $eleve->getId(),
                        'nom' => $eleve->getNom(),
                        'prenom' => $eleve->getPrenom(),
                        'birthday' => $eleve->getBirthday(),
                        'solde_initial' => $eleve->getSoldeInitial(),
                        'email_parent' => $eleve->getEmailParent(),
                        "classe" => [
                            "id" => $eleve->getClasse()->getId(),
                            "nom" => $eleve->getClasse()->getNom(),
                            "niveau" => $eleve->getClasse()->getNiveau(),
                            "nbMax" => $eleve->getClasse()->getNbMax(),

                        ],
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
                "professeurs" => array_map(function ($prof) {
                    return [
                        'id' => $prof->getId(),
                        'nom' => $prof->getNom(),
                        'prenom' => $prof->getPrenom(),
                    ];
                }, $profs)
            ];
        }, $ecoles);
        return $this->json(["ecoles" => $data]);
    }
    #[Route('/api/ecoles', name: 'add_ecole', methods: ['POST'])]
    public function add(UserRepository $userRepository, Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data, $data['nom'], $data['user_id'])) {
            return $this->json(['invalid payload', 400]);
        }
        $user_id = intval($data['user_id']);
        $user = $userRepository->find($user_id);
        if (!$user) {
            return $this->json(["user not found", 404]);
        }
        $ecole = new Ecole();
        $ecole->setNom($data['nom']);
        $ecole->setUtilisateur($user);
        $em->persist($ecole);
        $em->flush();
        return $this->json([
            "ecole created successfully",
            'id' => $ecole->getId(),
            'nom' => $ecole->getNom(),
        ]);
    }
}

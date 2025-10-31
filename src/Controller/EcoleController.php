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
                "phone" => $ecole->getPhone(),
                "directeur" => $ecole->getDirecteur(),
                'classes' => array_map(function ($classe) {
                    $matieres = $classe->getMatieres()->toArray();
                    $eleves = $classe->getEleves()->toArray();
                    return [
                        'id' => $classe->getId(),
                        'nom' => $classe->getNom(),
                        'niveau' => $classe->getNiveau(),
                        'frais' => $classe->getFrais(),
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
                                        'grades' => array_map(function ($grade) {
                                            return [
                                                'id' => $grade->getId(),
                                                'note' => $grade->getNote(),
                                                'note_maximal' => $grade->getNoteMaximal(),
                                                'type_examen' => $grade->getTypeExamen(),
                                                'date' => $grade->getDate(),
                                                'matiere' => $grade->getMatiere() ? [
                                                    'id' => $grade->getMatiere()->getId(),
                                                    'nom' => $grade->getMatiere()->getNom(),
                                                    'coef' => $grade->getMatiere()->getCoefficient(),
                                                ] : null,
                                            ];
                                        }, $bulletin->getGradePs()->toArray())
                                    ];
                                }, $eleve->getBulletins()->toArray()),
                                "classe" => [
                                    "id" => $eleve->getClasse()->getId(),
                                    "nom" => $eleve->getClasse()->getNom(),
                                    "niveau" => $eleve->getClasse()->getNiveau(),

                                ]
                            ];
                        }, $eleves),
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

                        ],
                        "bulletins" => array_map(function ($bulletin) {
                            return [
                                'id' => $bulletin->getId(),
                                'grades' => array_map(function ($grade) {
                                    return [
                                        'id' => $grade->getId(),
                                        'note' => $grade->getNote(),
                                        'note_maximal' => $grade->getNoteMaximal(),
                                        'type_examen' => $grade->getTypeExamen(),

                                        'date' => $grade->getDate(),
                                        'matiere' => $grade->getMatiere() ? [
                                            'id' => $grade->getMatiere()->getId(),
                                            'nom' => $grade->getMatiere()->getNom(),
                                            'coef' => $grade->getMatiere()->getCoefficient(),
                                        ] : null,
                                    ];
                                }, $bulletin->getGradePs()->toArray() ?? $bulletin->getGradeHs()->toArray())
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
        if (!isset($data, $data['nom'], $data['user_id'], $data['phone'], $data['directeur'])) {
            return $this->json(['invalid payload', 400]);
        }
        $user_id = intval($data['user_id']);
        $user = $userRepository->find($user_id);
        if (!$user) {
            return $this->json(["user not found", 404]);
        }
        $ecole = new Ecole();
        $ecole->setNom($data['nom']);
        $ecole->setPhone($data['phone']);
        $ecole->setDirecteur($data['directeur']);
        $ecole->setUtilisateur($user);
        $em->persist($ecole);
        $em->flush();
        return $this->json([
            "ecole created successfully",
            'id' => $ecole->getId(),
            'nom' => $ecole->getNom(),
            'phone' => $ecole->getPhone(),
            'directeur' => $ecole->getDirecteur(),
        ]);
    }
    #[Route('/api/ecoles/id/{id}', name: 'show_ecole', methods: ['GET'])]
    public function show(EcoleRepository $ecoleRepository, Request $request, int $id): Response
    {
        // 1. Fetch the user by ID
        $school_id = intval($id);
        $ecole = $ecoleRepository->find($school_id);

        if (!$ecole) {
            return $this->json(["message" => "Ecole introuvable"], 404);
        }


        // 3. Structure data for each school
        $classes = $ecole->getClasse()->toArray();
        $eleves = $ecole->getEleves()->toArray();
        $profs = $ecole->getProfesseurs()->toArray();
        $data = [

            'id' => $ecole->getId(),
            'nom' => $ecole->getNom(),
            "phone" => $ecole->getPhone(),
            "directeur" => $ecole->getDirecteur(),
            'classes' => array_map(function ($classe) {
                $matieres = $classe->getMatieres()->toArray();
                $eleves = $classe->getEleves()->toArray();

                // Prepare class data
                return [
                    'id' => $classe->getId(),
                    'nom' => $classe->getNom(),
                    'niveau' => $classe->getNiveau(),
                    'frais' => $classe->getFrais(),

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
                            "classe" => [
                                "id" => $eleve->getClasse()->getId(),
                                "nom" => $eleve->getClasse()->getNom(),
                                "niveau" => $eleve->getClasse()->getNiveau(),

                            ],
                            "bulletins" => array_map(function ($bulletin) {
                                return [
                                    'id' => $bulletin->getId(),
                                    'gradesH' => array_map(function ($grade) {
                                        return [
                                            'id' => $grade->getId(),
                                            'note' => $grade->getNote(),
                                            'type' => $grade->getType(),

                                            'cycle' => $grade->getCycle() ? [] : null,
                                            'date' => $grade->getDate(),
                                            'matiere' => $grade->getMatiere() ? [
                                                'id' => $grade->getMatiere()->getId(),
                                                'nom' => $grade->getMatiere()->getNom(),
                                                'coef' => $grade->getMatiere()->getCoefficient(),
                                            ] : null,
                                        ];
                                    }, $bulletin->getGradeHs()->toArray()),
                                    'gradesP' => array_map(function ($grade) {
                                        return [
                                            'id' => $grade->getId(),
                                            'note' => $grade->getNote(),
                                            'mois' => $grade->getMois(),

                                            'date' => $grade->getDate(),
                                            'matiere' => $grade->getMatiere() ? [
                                                'id' => $grade->getMatiere()->getId(),
                                                'nom' => $grade->getMatiere()->getNom(),
                                                'coef' => $grade->getMatiere()->getCoefficient(),
                                            ] : null,
                                        ];
                                    }, $bulletin->getGradePs()->toArray())
                                ];
                            }, $eleve->getBulletins()->toArray()),
                        ];
                    }, $eleves),

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

                    ],
                    "bulletins" => array_map(function ($bulletin) {
                        $eleve = $bulletin->getEleve();
                        return [
                            'id' => $bulletin->getId(),
                            'classe' => $bulletin->getClasse()->getNom(),
                            'eleve' => [
                                'id' => $eleve->getId(),
                                'nom' => $eleve->getNom(),
                                'prenom' => $eleve->getPrenom(),
                                'birthday' => $eleve->getBirthday(),
                                'solde_initial' => $eleve->getSoldeInitial(),
                                'email_parent' => $eleve->getEmailParent(),
                            ],
                            "cycles" => array_map(function ($cycle) {
                                return [
                                    "id" => $cycle->getId(),
                                    'libelle' => $cycle->getLibelle(),
                                    'gradeH' => array_map(function ($gradeh) {
                                        return [
                                            'id' => $gradeh->getId(),
                                            'type' => $gradeh->getType(),
                                            "matiere" => $gradeh->getMatiere() ? [
                                                'id' => $gradeh->getMatiere()->getId(),
                                                'coef' => $gradeh->getMatiere()->getCoefficient(),
                                            ] : null,
                                            "date" => $gradeh->getDate(),
                                        ];
                                    }, $cycle->getGradeHs()->toArray())
                                ];
                            }, $bulletin->getCycles()->toArray()),
                            "gradeP" => array_map(function ($gradep) {
                                return [
                                    'id' => $gradep()->getId(),
                                    'note' => $gradep->getNote(),
                                    'mois' => $gradep->getMois(),
                                    "matiere" => $gradep->getMatiere() ? [
                                        'id' => $gradep->getMatiere()->getId(),
                                        'coef' => $gradep->getMatiere()->getCoefficient(),
                                    ] : null,

                                ];
                            }, $bulletin->getGradePs()->toArray())

                        ];
                    }, $eleve->getBulletins()->toArray()),
                ];
            }, $eleves),
            "professeurs" => array_map(function ($prof) {
                return [
                    'id' => $prof->getId(),
                    'nom' => $prof->getNom(),
                    'prenom' => $prof->getPrenom(),
                    'telephone' => $prof->getTelephone(),
                ];
            }, $profs)
        ];

        // 4. Return the structured data as JSON
        return $this->json(["ecole" => $data]);
    }
    #[Route('/api/ecoles/edit/{id}', name: 'edit_ecole', methods: ['PUT'])]
    public function edit(EcoleRepository $ecoleRepository, Request $request, EntityManagerInterface $em, int $id): Response
    {
        $data = json_decode($request->getContent(), true);
        $ecole_id = intval($id);
        $ecole = $ecoleRepository->find($ecole_id);
        if (!$ecole) {
            return $this->json(["ecole not found", 404]);
        }
        $ecole->setNom($data['nom'] ?? $ecole->getNom());
        $em->flush();
        return $this->json([
            "ecole edited successfully",
            'id' => $ecole->getId(),
            'nom' => $ecole->getNom(),
        ]);
    }
}

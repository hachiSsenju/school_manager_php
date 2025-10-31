<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Entity\Cycle;
use App\Entity\Trimester;
use App\Repository\ClasseRepository;
use App\Repository\EcoleRepository;
use App\Repository\EleveRepository;
use App\Repository\ProfesseurRepository;
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
            
            $ecole = $classe->getEcole();
            $ecoleClasses = $ecole->getClasse()->toArray();
            $ecolePRofs = $ecole->getProfesseurs()->toArray();
            return [
                'id'     => $classe->getId(),
                'nom'    => $classe->getNom(),
                'niveau' => $classe->getNiveau(),
                'frais'  => $classe->getFrais(),

                'matieres' => array_map(function ($matiere) {
                    return [
                        'id' => $matiere->getId(),
                        'nom' => $matiere->getNom(),
                        'coefficient' => $matiere->getCoefficient(),
                    ];
                }, $matieres),
                'professeurs' => array_map(function ($prof) {
                return [
                    "classe"=>$prof->getClasse()->getNom(),
                    'id' => $prof->getId(),
                    'nom' => $prof->getNom(),
                    'prenom' => $prof->getPrenom(),
                    'telephone' => $prof->getTelephone(),
                ];
            }, $classe->getProfesseur()->toArray()),

                "ecole" => [
                    'id' => $ecole->getId(),
                    'nom' => $ecole->getNom(),
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
                            
                        ];
                    }, $ecoleClasses),
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
                        ];
                    }, $ecolePRofs)
                ],

                'eleves' => array_map(function ($eleve) {
                    return [
                        'id' => $eleve->getId(),
                        'nom' => $eleve->getNom(),
                        'prenom' => $eleve->getPrenom(),
                        'birthday' => $eleve->getBirthday(),
                        'solde_initial' => $eleve->getSoldeInitial(),
                        'email_parent' => $eleve->getEmailParent(),
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
        
        $data = [
            'id'     => $classe->getId(),
            'nom'    => $classe->getNom(),
            'niveau' => $classe->getNiveau(),
            'frais'  => $classe->getFrais(),

            'matieres' => array_map(function ($matiere) {
                return [
                    'id' => $matiere->getId(),
                    'nom' => $matiere->getNom(),
                    'coefficient' => $matiere->getCoefficient(),
                ];
            }, $classe->getMatieres()->toArray()),
            
            'professeurs' => array_map(function ($prof) {
                return [
                    'id' => $prof->getId(),
                    'nom' => $prof->getNom(),
                    'prenom' => $prof->getPrenom(),
                    'telephone' => $prof->getTelephone(),
                ];
            }, $classe->getProfesseur()->toArray()),

            'eleves' => array_map(function ($eleve) {
                return [
                    'id' => $eleve->getId(),
                    'nom' => $eleve->getNom(),
                    'prenom' => $eleve->getPrenom(),
                    'birthday' => $eleve->getBirthday(),
                    'solde_initial' => $eleve->getSoldeInitial(),
                    'email_parent' => $eleve->getEmailParent(),
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
            }, $classe->getEleves()->toArray()),
        ];
        return $this->json($data);
    }


    #[Route('/api/classes/student/{id}', name: 'getbystudent_classe', methods: ['GET'])]
    public function getbystudent(EleveRepository $eleveRepository, int $id): JsonResponse
    {
        $eleve_id = intval($id);
        $eleve = $eleveRepository->find($eleve_id);
        if (!$eleve) {
            return $this->json(['error' => 'Eleve not found'], 404);
        }
        $classe = $eleve->getClasse();
        $data =
            $matieres = $classe->getMatieres()->toArray();
        
        $data = [
            'id'     => $classe->getId(),
            'nom'    => $classe->getNom(),
            'niveau' => $classe->getNiveau(),
            'frais'  => $classe->getFrais(),

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
            }, $classe->getEleves()->toArray()),
        ];
        return $this->json($data);
    }

    #[Route('/api/classes', name: 'add_classe', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em, EcoleRepository $ecoleRepository, ProfesseurRepository $professeurRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (
            !$data ||
            !isset($data['nom'], $data['niveau'], $data['frais'], $data['ecole_id'],)
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
        $classe->setEcole($ecole);
        // Handle responsables for primaire and maternelle
        if (($data['niveau'] === 'primaire' || $data['niveau'] === 'maternelle') && isset($data['responsables']) && is_array($data['responsables'])) {
            foreach ($data['responsables'] as $profId) {
                $professeur = $professeurRepository->find($profId);
                if ($professeur) {
                    $classe->addProfesseur($professeur);
                }
            }
        }
        if ($data['niveau'] === 'primaire' || $data['niveau'] === 'maternelle') {
            $trimester = new Trimester();
            $trimester->setClasse($classe);
            $trimester->setLibelle('Niveau 1');
            $trimester2 = new Trimester();
            $trimester2->setClasse($classe);
            $trimester2->setLibelle('Niveau 2');
            $trimester3 = new Trimester();
            $trimester3->setClasse($classe);
            $trimester3->setLibelle('Niveau 3');
            $em->persist($trimester);
            $em->persist($trimester2);
            $em->persist($trimester3);
        }
        if ($data['niveau'] == 'lycee' || $data['niveau'] == 'college') {
            $trimester = new Trimester();
            $trimester->setClasse($classe);
            $trimester->setLibelle('1er Cycle');
            $trimester2 = new Trimester();
            $trimester2->setClasse($classe);
            $trimester2->setLibelle('2ème Cycle');
            $em->persist($trimester);
            $em->persist($trimester2);
        }
        $em->persist($classe);
        $em->flush();
        
        $data = [
            'id'     => $classe->getId(),
            'nom'    => $classe->getNom(),
            'niveau' => $classe->getNiveau(),
            'frais'  => $classe->getFrais(),
           
            'professeurs' => array_map(function ($prof) {
                return [
                    'id' => $prof->getId(),
                    'nom' => $prof->getNom(),
                    'prenom' => $prof->getPrenom(),
                    'telephone' => $prof->getTelephone(),
                ];
            }, $classe->getProfesseur()->toArray()),

        ];
        return $this->json($data);
    }

    #[Route('/api/classes/edit/{id}', name: 'edit_classe', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $em, int $id, ProfesseurRepository $professeurRepository): JsonResponse
    {
        $classe_id = intval($id);
        $classe = $em->getRepository(Classe::class)->find($classe_id);
        if (!$classe) {
            return $this->json(['error' => 'Classe not found'], 404);
        }
        $data = json_decode($request->getContent(), true);
        $classe->setNom($data['nom'] ?? $classe->getNom());
        $classe->setNiveau($data['niveau'] ?? $classe->getNiveau());
        $classe->setFrais($data['frais'] ?? $classe->getFrais());
        // Handle responsables update for primaire and maternelle
        $niveau = $data['niveau'] ?? $classe->getNiveau();
        if ($niveau === 'primaire' || $niveau === 'maternelle') {
            // Clear existing responsables
            foreach ($classe->getProfesseur() as $professeur) {
                $classe->removeProfesseur($professeur);
            }

            // Add new responsables if provided
            if (isset($data['responsables']) && is_array($data['responsables'])) {
                foreach ($data['responsables'] as $profId) {
                    $professeur = $professeurRepository->find($profId);
                    if ($professeur) {
                        $classe->addProfesseur($professeur);
                    }
                }
            }
        }
        $em->flush();
        return $this->json([
            'message' => 'Classe edited successfully',
            'id' => $classe->getId(),
            'nom' => $classe->getNom(),
            'frais' => $classe->getFrais(),
            'niveau' => $classe->getNiveau(),
            'professeurs' => array_map(function ($prof) {
                return [
                    'id' => $prof->getId(),
                    'nom' => $prof->getNom(),
                    'prenom' => $prof->getPrenom(),
                    'telephone' => $prof->getTelephone(),
                ];
            }, $classe->getProfesseur()->toArray()),

        ], 201);
    }
    #[Route('/api/classes/delete/{id}', name: 'delete_classe', methods: ['DELETE'])]
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
    //  #[Route('/api/classes/matieres/{id}', name: 'matieres_classes', methods: ['GET'])]
    // public function matieresClasses(ClasseRepository $classeRepository, int $id): JsonResponse
    // {}
}

<?php

namespace App\Controller;

use App\Entity\Bulletin;
use App\Entity\Cycle;
use App\Repository\BulletinRepository;
use App\Repository\ClasseRepository;
use App\Repository\EcoleRepository;
use App\Repository\EleveRepository;
use App\Repository\TrimesterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BulletinController extends AbstractController
{

    #[Route('/api/bulletins', name: 'add_bulletin', methods: ['POST'])]
    public function add(Request $request, EcoleRepository $ecoleRepository, EleveRepository $eleveRepository, ClasseRepository $classeRepository, TrimesterRepository $trimesterRepository, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['eleve_id'], $data['classe_id'], $data['ecole_id'], $data['annee_scolaire'], $data['date'])) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }
        $eleve_id = intval($data['eleve_id']);
        $ecole_id = intval($data['ecole_id']);
        $classe_id = intval($data['classe_id']);
        $eleve = $eleveRepository->find($eleve_id);
        $classe = $classeRepository->find($classe_id);
        $ecole = $ecoleRepository->find($ecole_id);
        if (!$eleve) {
            return $this->json(['error' => 'Eleve not found'], 404);
        }
        if (!$classe) {
            return $this->json(['error' => 'Classe not found'], 404);
        }
        if (!$ecole) {
            return $this->json(['error' => 'Ecole not found'], 404);
        }
        $bulletin = new Bulletin();
        $bulletin->setEleve($eleve);
        $bulletin->setClasse($classe);
        $bulletin->setRedoublant(false);
        $bulletin->setAnneeScholaire($data['annee_scolaire']);
        $bulletin->setEcole($ecole);
        $bulletin->setMention('');
        $bulletin->setRang('');
        $bulletin->setMoyAnnuelle('');
        $bulletin->setHeureAbsence('');
        $bulletin->setDate($data['date']);
        if ($classe->getNiveau() == "lycee" || $classe->getNiveau() == 'college') {
            $cyle1 = new Cycle();
            $cyle1->setLibelle('Premier');
            $cyle2 = new Cycle();
            $cyle2->setLibelle('Deuxieme');
            $cyle3 = new Cycle();
            $cyle3->setLibelle('Troisieme');
            $bulletin->addCycle($cyle1);
            $bulletin->addCycle($cyle2);
            $bulletin->addCycle($cyle3);
        }
        $em->persist($bulletin);
        $em->flush();
        $cycles = $bulletin->getCycles()->toArray();
        $data = [
            'id' => $bulletin->getId(),
            'eleve' => $bulletin->getEleve() ? [
                'id' => $bulletin->getEleve()->getId(),
                'nom' => $bulletin->getEleve()->getNom(),
                'prenom' => $bulletin->getEleve()->getPrenom(),
            ] : null,
            'classe' => $bulletin->getClasse() ? [
                'id' => $bulletin->getClasse()->getId(),
                'nom' => $bulletin->getClasse()->getNom(),
                'niveau' => $bulletin->getClasse()->getNiveau(),
            ] : null,
            'Cycles' => array_map(function ($cycle) {
                return [
                    'id' => $cycle->getId(),
                    'libelle' => $cycle->getLibelle(),
                ];
            }, $cycles),
        ];
        return $this->json($data);
    }
    #[Route('/api/bulletins', name: 'get_bulletin', methods: ['GET'])]
    public function list(BulletinRepository $bulletinRepository): Response
    {
        $bulletins = $bulletinRepository->findAll();
        $data = array_map(function ($bulletin) {
            $eleve = $bulletin->getEleve();
            return [
                'id' => $bulletin->getId(),
                'trimester' => $bulletin->getTrimester()->getLibelle(),
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
                                "trimester" => $gradeh->getTrimester() ? [
                                    'id' => $gradeh->getTrimester()->getId(),
                                    'libelle' => $gradeh->getTrimester()->getLibelle(),
                                ] : null,
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
        }, $bulletins);
        return $this->json($data);
    }


    #[Route('/api/bulletins/classe/{id}', name: 'getbyclasse_bulletin', methods: ['GET'])]
    public function byClasse(ClasseRepository $classeRepository, int $id): Response
    {
        $classe_id = intval($id);
        $classe = $classeRepository->find($classe_id);
        $data = array_map(function ($bulletin) {
            $eleve = $bulletin->getEleve();
            $eleve = $bulletin->getEleve();
            return [
                'id' => $bulletin->getId(),
                'trimester' => $bulletin->getTrimester()->getLibelle(),
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
                                "trimester" => $gradeh->getTrimester() ? [
                                    'id' => $gradeh->getTrimester()->getId(),
                                    'libelle' => $gradeh->getTrimester()->getLibelle(),
                                ] : null,
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
        }, $classe->getBulletins()->toArray());
        return $this->json($data);
    }
    #[Route('/api/bulletins/eleve/{id}', name: 'getbyeleve_bulletin', methods: ['GET'])]
    public function byEleve(EleveRepository $eleveRepository, int $id): Response
    {
        $eleve_id = intval($id);
        $eleve = $eleveRepository->find($eleve_id);
        $data = array_map(function ($bulletin) {
            $eleve = $bulletin->getEleve();
            $eleve = $bulletin->getEleve();
            return [
                'id' => $bulletin->getId(),
                'trimester' => $bulletin->getTrimester()->getLibelle(),
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
                                "trimester" => $gradeh->getTrimester() ? [
                                    'id' => $gradeh->getTrimester()->getId(),
                                    'libelle' => $gradeh->getTrimester()->getLibelle(),
                                ] : null,
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
        }, $eleve->getBulletins()->toArray());
        return $this->json($data);
    }
    #[Route('/api/bulletins/trimester/{id}', name: 'get_bulletin_by_trimester_id', methods: ['GET'])]
    #[Route('/api/bulletins/trimester/{id}', name: 'app_bulletins_by_trim', methods: ['POST'])]
    public function getByTrim(
        TrimesterRepository $trimesterRepository,
        int $id,
        Request $request,
        EleveRepository $eleveRepository,
        BulletinRepository $bulletinRepository
    ): Response {
        $requestData = json_decode($request->getContent(), true);

        // Validate payload
        if (!$requestData || !isset($requestData['eleve_id'])) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }

        $trimester_id = intval($id);
        $eleve_id = intval($requestData['eleve_id']);

        // Fetch entities
        $eleve = $eleveRepository->find($eleve_id);
        if (!$eleve) {
            return $this->json(['error' => 'Élève not found'], 404);
        }

        $trimester = $trimesterRepository->find($trimester_id);
        if (!$trimester) {
            return $this->json(['error' => 'Trimestre not found'], 404);
        }

        // Get the first bulletin that matches both eleve and trimester
        $bulletin = $bulletinRepository->findOneBy([
            'eleve' => $eleve,
            'trimester' => $trimester
        ]);

        // Handle case when no bulletin found
        if (!$bulletin) {
            return $this->json(['message' => 'No bulletin found for this student and trimester'], 404);
        }
//  id: string;
//   nom: string;
//   classe: Classe;
//   bulletins: Bulletin[];
//   grades: Grade[];
        // Map response data
        $eleve = $bulletin->getEleve();
        $data = [
            'id' => $bulletin->getId(),
            'trimester' => [
                "id"=>$bulletin->getTrimester()->getId(),
                "nom"=>$bulletin->getTrimester()->getLibelle(),
            ],
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
                            'note' =>$gradeh->getNote(),
                            'id' => $gradeh->getId(),
                            'type' => $gradeh->getType(),
                            "matiere" => $gradeh->getMatiere() ? [
                                'id' => $gradeh->getMatiere()->getId(),
                                'coef' => $gradeh->getMatiere()->getCoefficient(),
                            ] : null,
                            "date" => $gradeh->getDate(),
                            "trimester" => $gradeh->getTrimester() ? [
                                'id' => $gradeh->getTrimester()->getId(),
                                'libelle' => $gradeh->getTrimester()->getLibelle(),
                            ] : null,
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

        return $this->json($data);
    }

    #[Route('/api/bulletins/delete/{id}', name: 'deleteBulletin_bulletin', methods: ['DELETE'])]
    public function deleteBulletin(EleveRepository $eleveRepository, int $id, Request $request, BulletinRepository $bulletinRepository, EntityManagerInterface $em): Response
    {
        $requestData = json_decode($request->getContent(), true);

        // Validate payload
        if (!$requestData || !isset($requestData['eleve_id'])) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }
        $eleve_id = intval($requestData['eleve_id']);
        $eleve = $eleveRepository->find($eleve_id);
        if (!$eleve) {
            return $this->json('Eleve not found', 404);
        }
        $bulletin_id = intval($id);
        $bulletin = $bulletinRepository->find($bulletin_id);
        if (!$bulletin) {
            return $this->json('bulletin not found', 404);
        }
        $em->remove($bulletin);
        $em->flush();
        $data = [
            'message' => 'deleted successfully',
            'id' => $bulletin->getId(),
        ];
        return $this->json($data, 20);
    }
}

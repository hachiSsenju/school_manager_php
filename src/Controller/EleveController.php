<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Entity\Eleve;
use App\Repository\ClasseRepository;
use App\Repository\EcoleRepository;
use App\Repository\EleveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EleveController extends AbstractController
{
    #[Route('/api/eleves', name: 'app_eleve', methods: ['GET'])]
    public function index(EleveRepository $eleveRepository): Response
    {
        $eleves = $eleveRepository->findAll();
        if (!$eleves) {
            return $this->json(['message' => 'No eleves found'], 404);
        }
        $data = array_map(function ($eleve) {
            $classe = $eleve->getClasse();
            $bulletins = $eleve->getBulletins()->toArray();
            return [
                'id' => $eleve->getId(),
                'nom' => $eleve->getNom(),
                'prenom' => $eleve->getPrenom(),
                'birthday' => $eleve->getBirthday(),
                'birthplace' => $eleve->getBirthplace(),
                'matricule' => $eleve->getMatricule(),
                'sexe' => $eleve->getSexe(),
                'solde_initial' => $eleve->getSoldeInitial(),
                'email_parent' => $eleve->getEmailParent(),
                'bulletins' => array_map(function ($bulletin) {
                    
                   
                }, $bulletins),
                'classe' => $classe ? [
                    'id' => $classe->getId(),
                    'nom' => $classe->getNom(),
                    'niveau' => $classe->getNiveau(),
                    'frais' => $classe->getFrais(),

                ] : null,
            ];
        }, $eleves);
        return $this->json($data);
    }
    #[Route('/api/eleves/{id}', name: 'get_eleve', methods: ['GET'])]
    public function show(EleveRepository $eleveRepository, int $id): Response
    {
        $eleve_id = intval($id);
        $eleve = $eleveRepository->find($eleve_id);
        if (!$eleve) {
            return $this->json(['error' => 'Eleve not found'], 404);
        }
        $classe = $eleve->getClasse();
        $ecole = $eleve->getEcole();
        $data = [
            'id' => $eleve->getId(),
            'nom' => $eleve->getNom(),
            'prenom' => $eleve->getPrenom(),
            'birthday' => $eleve->getBirthday(),
            'solde_initial' => $eleve->getSoldeInitial(),
            'birthplace' => $eleve->getBirthplace(),
            'matricule' => $eleve->getMatricule(),
            'sexe' => $eleve->getSexe(),
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
                                    "note" => $gradeh->getNote(),
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
            'email_parent' => $eleve->getEmailParent(),
            'classe' => $classe ? [
                'id' => $classe->getId(),
                'nom' => $classe->getNom(),
                'niveau' => $classe->getNiveau(),
                'frais' => $classe->getFrais(),
                 

            ] : null,
            "ecole" =>[
                "id"=> $ecole->getId(),
                'nom'=>$ecole->getNom(),
                'phone'=> $ecole->getPhone(),
                'directeur'=>$ecole->getDirecteur(),
            ]
        ];
        return $this->json($data);
    }
    #[Route('/api/eleves', name: 'add_eleve', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em, EcoleRepository $ecoleRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!$data || !isset(
            $data['nom'],
            $data['prenom'],
            $data['birthday'],
            $data['solde_initial'],
            $data['classe_id'],
            $data['ecole_id'],
            $data['birthplace'],
            $data['matricule'],
            $data['sexe']
        )) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }
        $ecole_id = intval($data['ecole_id']);
        $ecole = $ecoleRepository->find($ecole_id);
        if (!$ecole) {
            return $this->json(['ecole not found', 404]);
        }
        $class_id = intval($data['classe_id']);
        $classe = $em->getRepository(Classe::class)->find($class_id);
        $eleve = new Eleve();
        $eleve->setNom($data['nom']);
        $eleve->setPrenom($data['prenom']);
        $eleve->setBirthday($data['birthday']);
        $eleve->setSoldeInitial($data['solde_initial']);
        $eleve->setSexe($data['sexe']);
        $eleve->setMatricule($data['matricule']);
        $eleve->setBirthplace($data['birthplace']);
        $eleve->setEmailParent($data['email_parent'] ?? null);
        $eleve->setClasse($classe);
        $eleve->setEcole($ecole);
        $em->persist($eleve);
        $em->flush();
        $data = [
            'id' => $eleve->getId(),
            'nom' => $eleve->getNom(),
            'prenom' => $eleve->getPrenom(),
            'birthday' => $eleve->getBirthday(),
            'solde_initial' => $eleve->getSoldeInitial(),
            'email_parent' => $eleve->getEmailParent(),
            'classe_id' => $eleve->getClasse() ? $eleve->getClasse()->getId() : null,
            'nom_ecole' => $eleve->getEcole() ? $eleve->getEcole()->getNom() : null,
        ];
        return $this->json($data, 201);
    }

    #[Route('/api/eleves/classe/{id}', name: 'getbyclasse_eleve', methods: ['GET'])]
    public function getByClass(int $id, ClasseRepository $classeRepository): Response
    {
        $classe_id = intval($id);

        $classe = $classeRepository->find($classe_id);
        if (!$classe) {
            return $this->json(['message' => 'No Classe found'], 404);
        }
        $data = array_map(function ($eleve) {
            $bulletins = $eleve->getBulletins()->toArray();
            return [
                'id' => $eleve->getId(),
                'nom' => $eleve->getNom(),
                'prenom' => $eleve->getPrenom(),
                'birthday' => $eleve->getBirthday(),
                'solde_initial' => $eleve->getSoldeInitial(),
                'email_parent' => $eleve->getEmailParent(),
                'bulletins' => array_map(function ($bulletin) {
                    
                   
                }, $bulletins),

            ];
        }, $classe->getEleves()->toArray());
        return $this->json($data);
    }
    #[Route('/api/eleves/edit/{id}', name: 'edit_eleve', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $data = json_decode($request->getContent(), true);
        $class_id = intval($data['classe_id']);
        $eleve_id = intval($id);
        $classe = $em->getRepository(Classe::class)->find($class_id);
        $eleve = $em->getRepository(Eleve::class)->find($eleve_id);
        $eleve->setNom($data['nom'] ?? $eleve->getNom());
        $eleve->setPrenom($data['prenom'] ?? $eleve->getPrenom());
        $eleve->setBirthday($data['birthday'] ?? $eleve->getBirthday());
        $eleve->setSoldeInitial($data['solde_initial'] ?? $eleve->getSoldeInitial());
        $eleve->setEmailParent($data['email_parent'] ?? $eleve->getEmailParent());
        $eleve->setClasse($classe ?? $eleve->getClasse());
        $em->flush();
        $data = [
            'id' => $eleve->getId(),
            'nom' => $eleve->getNom(),
            'prenom' => $eleve->getPrenom(),
            'birthday' => $eleve->getBirthday(),
            'solde_initial' => $eleve->getSoldeInitial(),
            'email_parent' => $eleve->getEmailParent(),
            'classe_id' => $eleve->getClasse() ? $eleve->getClasse()->getId() : null,
        ];
        return $this->json($data, 202);
    }
    #[Route('/api/eleves/delete/{id}', name: 'delete_eleve', methods: ['DELETE'])]
    public function delete(Request $request, EntityManagerInterface $em, int $id): Response
    {

        $eleve_id = intval($id);
        $eleve = $em->getRepository(Eleve::class)->find($eleve_id);
        if (!$eleve) {
            return $this->json(['error' => 'Eleve not found'], 404);
        }
        $em->remove($eleve);
        $em->flush();
        $data = [
            'message' => 'Eleve deleted successfully',
        ];
        return $this->json($data, 202);
    }
    #[Route('/api/eleves/transfert/{id}', name: 'share_eleve', methods: ['POST'])]
    public function transfert(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['new_classe'])) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }
        $new_class_id = intval($data['new_classe']);
        $eleve_id = intval($id);
        $classe = $em->getRepository(Classe::class)->find($new_class_id);
        $eleve = $em->getRepository(Eleve::class)->find($eleve_id);
        if (!$eleve) {
            return $this->json(['error' => 'Eleve not found'], 404);
        }
        if (!$classe) {
            return $this->json(['error' => 'Classe not found'], 404);
        }
        $eleve->setClasse($classe);
        $em->flush();
        $data = [
            'id' => $eleve->getId(),
            'nom' => $eleve->getNom(),
            'prenom' => $eleve->getPrenom(),
            'birthday' => $eleve->getBirthday(),
            'solde_initial' => $eleve->getSoldeInitial(),
            'email_parent' => $eleve->getEmailParent(),
            'classe_id' => $eleve->getClasse() ? $eleve->getClasse()->getId() : null,
        ];
        return $this->json($data, 202);
    }



     #[Route('/api/classe/eleve/{id}', name: 'getCLasse_by_eleveid', methods: ['GET'])]
    public function getClasseByEleveId(Request $request, EntityManagerInterface $em, int $id,EleveRepository $eleveRepository): Response
    {
        $eleve_id = intval($id);
        $eleve = $eleveRepository->find($eleve_id);
        $classe = $eleve->getClasse();
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
            },$classe->getMatieres()->toArray()),
            
            
        ];
        return $this->json($data);
    
    }
}

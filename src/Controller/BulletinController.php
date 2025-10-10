<?php

namespace App\Controller;

use App\Entity\Bulletin;
use App\Repository\BulletinRepository;
use App\Repository\ClasseRepository;
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
    public function add(Request $request, EleveRepository $eleveRepository, ClasseRepository $classeRepository, TrimesterRepository $trimesterRepository, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['eleve_id'], $data['classe_id'], $data['trimester_id'])) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }
        $eleve_id = intval($data['eleve_id']);
        $classe_id = intval($data['classe_id']);
        $trimester_id = intval($data['trimester_id']);
        $eleve = $eleveRepository->find($eleve_id);
        $classe = $classeRepository->find($classe_id);
        $trimester = $trimesterRepository->find($trimester_id);
        if (!$eleve) {
            return $this->json(['error' => 'Eleve not found'], 404);
        }
        if (!$classe) {
            return $this->json(['error' => 'Classe not found'], 404);
        }
        if (!$trimester) {
            return $this->json(['error' => 'Trimester not found'], 404);
        }
        $bulletin = new Bulletin();
        $bulletin->setEleve($eleve);
        $bulletin->setClasse($classe);
        $bulletin->setTrimester($trimester);
        $em->persist($bulletin);
        $em->flush();
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
            'trimester' => $bulletin->getTrimester() ? [
                'id' => $bulletin->getTrimester()->getId(),
                'libelle' => $bulletin->getTrimester()->getLibelle()
            ] : null,
        ];
        return $this->json($data);
    }
    #[Route('/api/bulletins', name: 'get_bulletin', methods: ['GET'])]
    public function list(BulletinRepository $bulletinRepository): Response
    {
        $bulletins = $bulletinRepository->findAll();
        $data = array_map(function ($bulletin) {
            $eleve = $bulletin->getEleve();
            $grades = $bulletin->getGrades();
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
                "grades" => array_map(function ($grade) {
                    return [
                        'id' => $grade->getId(),
                        'note' => $grade->getNote(),
                        'note_maximal' => $grade->getNoteMaximal(),
                        'type_examen' => $grade->getTypeExamen(),
                        'trimestre' => $grade->getTrimester() ? [
                            'id' => $grade->getTrimester()->getId(),
                            'libelle' => $grade->getTrimester()->getLibelle(),
                        ] : null,
                        'date' => $grade->getDate(),
                        'matiere' => $grade->getMatiere() ? [
                            'id' => $grade->getMatiere()->getId(),
                            'nom' => $grade->getMatiere()->getNom(),
                            'coef' => $grade->getMatiere()->getCoefficient(),
                        ] : null,
                    ];
                }, $bulletin->getGrades()->toArray())
            ];
        }, $bulletins);
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

        // Map response data
        $eleve = $bulletin->getEleve();
        $grades = $bulletin->getGrades()->toArray();

        $data = [
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
            'grades' => array_map(function ($grade) {
                return [
                    'id' => $grade->getId(),
                    'note' => $grade->getNote(),
                    'note_maximal' => $grade->getNoteMaximal(),
                    'type_examen' => $grade->getTypeExamen(),
                    'trimestre' => $grade->getTrimester() ? [
                        'id' => $grade->getTrimester()->getId(),
                        'libelle' => $grade->getTrimester()->getLibelle(),
                    ] : null,
                    'date' => $grade->getDate(),
                    'matiere' => $grade->getMatiere() ? [
                        'id' => $grade->getMatiere()->getId(),
                        'nom' => $grade->getMatiere()->getNom(),
                        'coef' => $grade->getMatiere()->getCoefficient(),
                    ] : null,
                ];
            }, $grades)
        ];

        return $this->json($data);
    }
}

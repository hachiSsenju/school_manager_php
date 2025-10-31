<?php

namespace App\Controller;

use App\Entity\Bulletin;
use App\Entity\Cycle;
use App\Entity\Eleve;
use App\Entity\Grade;
use App\Entity\GradeH;
use App\Entity\Matiere;
use App\Entity\Trimester;
use App\Repository\GradeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GradeController extends AbstractController
{
    #[Route('/api/grades', name: 'app_grade', methods: ['GET'])]
    public function index(GradeRepository $gradeRepository): Response
    {
        $grades = $gradeRepository->findAll();
        if (!$grades) {
            return $this->json(['message' => 'No grades found'], 404);
        }
        $data = array_map(function ($grade) {
            $eleve = $grade->getEleve();
            $matiere = $grade->getMatiere();
            return [
                'id' => $grade->getId(),
                'note' => $grade->getNote(),
                'note_maximal' => $grade->getNoteMaximal(),
                'type_examen' => $grade->getTypeExamen(),
                'trimestre' => $grade->getTrimester(),
                'date' => $grade->getDate(),
                'eleve' => $eleve ? [
                    'id' => $eleve->getId(),
                    'nom' => $eleve->getNom(),
                    'prenom' => $eleve->getPrenom(),
                ] : null,
                'matiere' => $matiere ? [
                    'id' => $matiere->getId(),
                    'nom' => $matiere->getNom(),
                    'coef' => $matiere->getCoefficient(),
                ] : null,
            ];
        }, $grades);
        return $this->json($data);
    }
    #[Route('/api/grades/{id}', name: 'show_grade', methods: ['GET'])]
    public function show(GradeRepository $gradeRepository, int $id): Response
    {
        $grade_id = intval($id);
        $grade = $gradeRepository->find($grade_id);
        if (!$grade) {
            return $this->json(['error' => 'Grade not found'], 404);
        }
        $data = [
            'id' => $grade->getId(),
            'note' => $grade->getNote(),
            'note_maximal' => $grade->getNoteMaximal(),
            'type_examen' => $grade->getTypeExamen(),
            'trimestre' => $grade->getTrimestre(),
            'date' => $grade->getDate(),
            'eleve' => $grade->getEleve() ? [
                'id' => $grade->getEleve()->getId(),
                'nom' => $grade->getEleve()->getNom(),
                'prenom' => $grade->getEleve()->getPrenom(),
            ] : null,
            'matiere' => $grade->getMatiere() ? [
                'id' => $grade->getMatiere()->getId(),
                'nom' => $grade->getMatiere()->getNom(),
                'coef' => $grade->getMatiere()->getCoefficient(),
            ] : null,
        ];
        return $this->json($data);
    }

 #[Route('/api/gradesH', name: 'add_gradeH', methods: ['POST'])]
    public function addGradeH(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['note'], $data['type'], $data['date'], $data['matiere_id'], $data["cycle_id"])) {
            return $this->json(['error' => 'Invalid data'], 400);
        }

        $matiere_id = intval($data['matiere_id']);
        $cycle_id = intval($data['cycle_id']);
        $matiere = $em->getRepository(Matiere::class)->find($matiere_id);
        $cycle = $em->getRepository(Cycle::class)->find($cycle_id);

        if ( !$matiere || !$cycle) {
            return $this->json(['error' => 'Eleve or Matiere not found'], 404);
        }

        // If $data["id"] is set and not null, edit the existing Grade
        if (!empty($data["id"])) {
            $grade = $em->getRepository(GradeH::class)->find(intval($data["id"]));
            if (!$grade) {
                return $this->json(['error' => 'Grade not found for editing'], 404);
            }
            $grade->setNote($data['note']);
            $grade->setType($data['type']);
            $grade->setDate($data['date']);
            $grade->setMatiere($matiere);
            $grade->setCycle($cycle);
            $em->flush();
            $status = 200;
            $message = 'Grade updated successfully';
        } else {
            // Otherwise, create a new Grade
            $grade = new GradeH();
            $grade->setNote($data['note']);
            $grade->setType($data['type']);
            $grade->setDate($data['date']);
            $grade->setMatiere($matiere);
            $grade->setCycle($cycle);
            $em->persist($grade);
            $em->flush();
            $status = 201;
            $message = 'Grade created successfully';
        }

        return $this->json([
            'message' => $message,
            'id' => $grade->getId(),
            'note' => $grade->getNote(),
            'type_examen' => $grade->getType(),
            'date' => $grade->getDate(),
            'matiere' => $grade->getMatiere() ? [
                'id' => $grade->getMatiere()->getId(),
                'nom' => $grade->getMatiere()->getNom(),
                'coef' => $grade->getMatiere()->getCoefficient(),
            ] : null,
            "bulletin" => $grade->getBulletin() ? [
                "id" => $grade->getBulletin()->getId(),
            ] : null
        ], $status);
    }



 #[Route('/api/gradesP', name: 'add_gradeH', methods: ['POST'])]
    public function addGradeP(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['note'], $data['type'], $data['matiere_id'], $data["bulletin_id"])) {
            return $this->json(['error' => 'Invalid data'], 400);
        }
        return $this->json(['message' => 'Not implemented yet'], 501);
    }

    
    

}

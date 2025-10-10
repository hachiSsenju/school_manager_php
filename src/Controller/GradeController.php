<?php

namespace App\Controller;

use App\Entity\Bulletin;
use App\Entity\Eleve;
use App\Entity\Grade;
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
    #[Route('/api/grades', name: 'add_grade', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['note'], $data['type_examen'], $data['trimester_id'], $data['date'], $data['eleve_id'], $data['matiere_id'], $data["bulletin_id"])) {
            return $this->json(['error' => 'Invalid data'], 400);
        }

        $eleve_id = intval($data['eleve_id']);
        $matiere_id = intval($data['matiere_id']);
        $trimestre_id = intval($data['trimester_id']);
        $bulletin_id = intval($data['bulletin_id']);
        $eleve = $em->getRepository(Eleve::class)->find($eleve_id);
        $matiere = $em->getRepository(Matiere::class)->find($matiere_id);
        $trimester = $em->getRepository(Trimester::class)->find($trimestre_id);
        $bulletin = $em->getRepository(Bulletin::class)->find($bulletin_id);

        if (!$eleve || !$matiere || !$trimester || !$bulletin) {
            return $this->json(['error' => 'Eleve or Matiere not found'], 404);
        }

        // If $data["id"] is set and not null, edit the existing Grade
        if (!empty($data["id"])) {
            $grade = $em->getRepository(Grade::class)->find(intval($data["id"]));
            if (!$grade) {
                return $this->json(['error' => 'Grade not found for editing'], 404);
            }
            $grade->setNote($data['note']);
            $grade->setNoteMaximal($data['note_maximal'] ?? $grade->getNoteMaximal());
            $grade->setTypeExamen($data['type_examen']);
            $grade->setTrimester($trimester);
            $grade->setDate($data['date']);
            $grade->setEleve($eleve);
            $grade->setMatiere($matiere);
            $grade->setBulletin($bulletin);
            $em->flush();
            $status = 200;
            $message = 'Grade updated successfully';
        } else {
            // Otherwise, create a new Grade
            $grade = new Grade();
            $grade->setNote($data['note']);
            $grade->setNoteMaximal($data['note_maximal'] ?? 40);
            $grade->setTypeExamen($data['type_examen']);
            $grade->setTrimester($trimester);
            $grade->setDate($data['date']);
            $grade->setEleve($eleve);
            $grade->setMatiere($matiere);
            $grade->setBulletin($bulletin);
            $em->persist($grade);
            $em->flush();
            $status = 201;
            $message = 'Grade created successfully';
        }

        return $this->json([
            'message' => $message,
            'id' => $grade->getId(),
            'note' => $grade->getNote(),
            'note_maximal' => $grade->getNoteMaximal(),
            'type_examen' => $grade->getTypeExamen(),
            'trimester' => $grade->getTrimester() ? [
                'id' => $grade->getTrimester()->getId(),
                'libelle' => $grade->getTrimester()->getLibelle(),
            ] : null,
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
            "bulletin" => $grade->getBulletin() ? [
                "id" => $grade->getBulletin()->getId(),
            ] : null
        ], $status);
    }


 #[Route('/api/grades/edit/{id}', name: 'edit_grade', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $em,GradeRepository $gradeRepository,int $id): Response
    {
        $data = json_decode($request->getContent(), true);
        $eleve_id = intval($data['eleve_id']);
        $matiere_id = intval($data['matiere_id']);
        $trimestre_id = intval($data['trimester_id']);
        $bulletin_id = intval($data['bulletin_id']);
        $eleve = $em->getRepository(Eleve::class)->find($eleve_id);
        $matiere = $em->getRepository(Matiere::class)->find($matiere_id);
        $trimester = $em->getRepository(Trimester::class)->find($trimestre_id);
        $bulletin = $em->getRepository(Bulletin::class)->find($bulletin_id);
        if (!$eleve || !$matiere || !$trimester || !$bulletin) {
            return $this->json(['error' => 'Eleve or Matiere  not found'], 404);
        }
        $grade_id = intval($id);
        $grade = $gradeRepository->find($grade_id);
        if(!$grade){
            return $this->json(["Error can't find a grade with this id"]);
        };
        $grade->setNote($data['note'] ?? $grade->getNote());
        $grade->setNoteMaximal($data["note_maximal"] ?? $grade->getNoteMaximal());
        $grade->setTypeExamen($data['type_examen'] ?? $grade->getTypeExamen());
        $grade->setDate($data['date'] ?? $grade->getDate());
        $grade->setTrimester($trimester);
        $grade->setEleve($eleve);
        $grade->setMatiere($matiere);
        $grade->setBulletin($bulletin);
        $em->flush();
        return $this->json([
            'message' => 'Grade edited successfully', 
            'id' => $grade->getId(),
            'note' => $grade->getNote(),
            'note_maximal' => $grade->getNoteMaximal(),
            'type_examen' => $grade->getTypeExamen(),
            'trimester' => $grade->getTrimester() ?[
                'id' => $grade->getTrimester()->getId(),
                'libelle' => $grade->getTrimester()->getLibelle(),
            ]: null,
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
            "bulletin" => $grade->getBulletin() ? [
                "id" => $grade->getBulletin()->getId(),
            ] : null
        ], 202);
    }

}

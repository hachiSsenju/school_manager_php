<?php

namespace App\Controller;

use App\Entity\Moyenne;
use App\Repository\CycleRepository;
use App\Repository\EleveRepository;
use App\Repository\MoyenneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MoyenneController extends AbstractController
{
    #[Route('/api/moyenne', name: 'app_moyenne', methods: ['GET'])]
    public function index(MoyenneRepository $moyenneRepository): Response
    {
        $moyennes = $moyenneRepository->findAll();
        $data = [];
        foreach ($moyennes as $moyenne) {
            $data[] = [
                "id" => $moyenne->getId(),
                "value" => $moyenne->getValue(),
                "pivot" => [
                    "eleve_id" => $moyenne->getEleve() ? $moyenne->getEleve()->getId() : null,
                    "cycle_id" => $moyenne->getCycle() ? $moyenne->getCycle()->getId() : null,
                ],
            ];
        }
        return $this->json($data);
    }

    #[Route('/api/moyenne/rank', name: 'rank_moyenne', methods: ['GET'])]
    public function rank(MoyenneRepository $moyenneRepository): Response
    {
        $moyennes = $moyenneRepository->findAll();

        // sort by value descending (largest first)
        usort($moyennes, function (Moyenne $a, Moyenne $b) {
            return $b->getValue() <=> $a->getValue();
        });

        $data = [];
        foreach ($moyennes as $index => $moyenne) {
            $data[] = [
                "rank" => $index + 1, // 1-based rank based on sorted order
                "id" => $moyenne->getId(),
                "value" => $moyenne->getValue(),
                "pivot" => [
                    "eleve_id" => $moyenne->getEleve() ? $moyenne->getEleve()->getId() : null,
                    "cycle_id" => $moyenne->getCycle() ? $moyenne->getCycle()->getId() : null,
                ],
            ];
        }

        return $this->json($data);
    }
    #[Route('api/moyenne', name: 'add_moyenne', methods: ['POST'])]
    public function add(Request $request, EleveRepository $eleveRepository, CycleRepository $cycleRepository, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['eleve_id'], $data['cycle_id'], $data['value'])) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }
        $eleve_id = intval($data['eleve_id']);
        $cycle_id = intval($data['cycle_id']);
        $value = floatval($data['value']);
        $eleve = $eleveRepository->find($eleve_id);
        if (!$eleve) {
            return $this->json(['error' => 'Eleve not found'], 404);
        }
        $cycle = $cycleRepository->find($cycle_id);
        if (!$cycle) {
            return $this->json(['error' => 'Cycle not found'], 404);
        }
        $moyenne = new Moyenne();
        $moyenne->setValue($value);
        $moyenne->setEleve($eleve);
        $moyenne->setCycle($cycle);
        $em->persist($moyenne);
        $em->flush();

        $data = [
            "id" => $moyenne->getId(),
            "value" => $moyenne->getValue(),
            "pivot" => [
                "eleve_id" => $eleve->getId(),
                "cycle_id" => $cycle->getId()
            ],
        ];
        return $this->json($data, 201);
    }
    //   #[Route('api/moyenne', name: 'list_moyenne')]
    // public function add(Request $request, EleveRepository $eleveRepository, CycleRepository $cycleRepository, EntityManagerInterface $em): Response
    // {
}

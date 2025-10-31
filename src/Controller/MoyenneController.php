<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Entity\Cycle;
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
    #[Route('/api/moyenne/classe/{id}', name: 'list_moyenne')]
    public function getMoyennesByClasseAndCycleId(int $id, Request $request, EleveRepository $eleveRepository, CycleRepository $cycleRepository, EntityManagerInterface $em): Response
    {
        $cycleIndex = intval($request->query->get('cycle_index', 0));
        $classe_id = intval($id);
        $classe = $em->getRepository(Classe::class)->find($classe_id);
        if (!$classe) {
            return $this->json(['error' => 'classe not found'], 404);
        }
        $eleves = $eleveRepository->findBy(['classe' => $classe]);
        $data = [];
        foreach ($eleves as $eleve) {
            $bulletins = $eleve->getBulletins();
            if (isset($bulletins[0])) {
                $cycles = $bulletins[0]->getCycles();
                if (isset($cycles[$cycleIndex])) {
                    $moyenne = $cycles[$cycleIndex]->getMoyenne();
                    $data[] = [
                        "value" => $moyenne,
                        "pivot" => [
                            "eleve_id" => $eleve->getId(),
                            "cycle_id" => $cycles[$cycleIndex]->getId(),
                        ],
                    ];
                }
            }
        }
        // Sort descending by 'value'
        usort($data, function ($a, $b) {
            return $b['value'] <=> $a['value'];
        });
        // Add rank and set it to the cycle using setRank
        foreach ($data as $i => &$item) {
            $rank = $i + 1;
            $item['rank'] = $rank;
            $cycle = $em->getRepository(Cycle::class)->find($item['pivot']['cycle_id']);
            if ($cycle) {
            $cycle->setRank($rank);
            }
        }
        unset($item);
        $em->flush();

        return $this->json(
            ["moyennes" => $data], 201);
        }


    #[Route('/api/moyenne', name: 'add_Moyenne', methods: ['POST'])]
    public function addMoyenne(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['cycle_id'], $data['value'])) {
            return $this->json(['error' => 'Invalid payload'], 400);
        }
        $cycle_id = intval($data['cycle_id']);
        $value = floatval($data['value']);
        $cycle = $em->getRepository(Cycle::class)->find($cycle_id);
        if (!$cycle) {
            return $this->json(['error' => 'Cycle not found'], 404);
        }
        $cycle->setMoyenne($value);
        $em->flush();

        $data = [
            "id" => $cycle->getId(),
            "value" => $cycle->getMoyenne(),
        ];
        return $this->json($data, 201);
    }
}

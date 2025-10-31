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
        $classe_id = intval($id);
        $classe = $em->getRepository(Classe::class)->find($classe_id);
        if (!$classe) {
            return $this->json(['error' => 'classe not found'], 404);
        }

        $eleves = $eleveRepository->findBy(['classe' => $classe]);

        // Group cycles by their position (cycle index) across each eleve's bulletins
        $groups = []; // keyed by cycle index (0-based)

        foreach ($eleves as $eleve) {
            $bulletins = $eleve->getBulletins();
            foreach ($bulletins as $bulletin) {
                $cycles = $bulletin->getCycles();
                foreach ($cycles as $index => $cycle) {
                    $groups[$index][] = [
                        'eleve_id' => $eleve->getId(),
                        'cycle_id' => $cycle->getId(),
                        'value' => $cycle->getMoyenne(),
                        'classe_nom' => $classe->getNom(), // include classe name per entry
                        'entity' => $cycle, // keep entity to set rank later
                    ];
                }
            }
        }

        // Prepare response structure: for each cycle position produce entries and assign ranks
        $result = [];
        foreach ($groups as $index => $entries) {
            // sort descending by value
            usort($entries, function ($a, $b) {
                return $b['value'] <=> $a['value'];
            });

            // assign ranks (1-based) and persist rank to each cycle entity
            foreach ($entries as $pos => &$entry) {
                $rank = $pos + 1;
                $entry['rank'] = $rank;
                if (isset($entry['entity']) && $entry['entity'] !== null) {
                    $entry['entity']->setRank($rank);
                }
                // remove entity from response payload
                unset($entry['entity']);
            }
            unset($entry);

            $result[] = [
                'cycle_position' => $index + 1, // human-friendly 1-based position
                'entries' => array_values($entries),
            ];
        }

        // persist all rank changes
        $em->flush();

        return $this->json(['cycles' => $result], 200);
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

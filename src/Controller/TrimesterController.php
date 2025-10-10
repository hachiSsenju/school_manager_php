<?php

namespace App\Controller;

use App\Repository\TrimesterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TrimesterController extends AbstractController
{
    #[Route('/trimester', name: 'app_trimester')]
    public function index(): Response
    {
        return $this->render('trimester/index.html.twig', [
            'controller_name' => 'TrimesterController',
        ]);
    }
    #[Route('/api/trimester/{id}', name: 'show_trimester',methods:['GET'])]
    public function show(int $id,TrimesterRepository $trimesterRepository): Response
    {
       $trimester_id = intval($id);
        $trimester = $trimesterRepository->find($id);
        if(!$trimester){
            return $this->json(["error cant's find trimeser",404]);
        }
        $data = [
            "id" => $trimester->getId(),
            "libelle" => $trimester->getLibelle(),
        ];
        return $this->json($data);
    }
}

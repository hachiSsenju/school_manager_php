<?php

namespace App\Entity;

use App\Repository\GradePRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GradePRepository::class)]
class GradeP
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $note = null;

    #[ORM\ManyToOne(inversedBy: 'gradePs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bulletin $Bulletin = null;

    #[ORM\ManyToOne(inversedBy: 'gradePs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Matiere $matiere = null;

    #[ORM\ManyToOne(inversedBy: 'gradeP')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mois $mois = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }

 
    public function getBulletin(): ?Bulletin
    {
        return $this->Bulletin;
    }

    public function setBulletin(?Bulletin $Bulletin): static
    {
        $this->Bulletin = $Bulletin;

        return $this;
    }

    public function getMatiere(): ?Matiere
    {
        return $this->matiere;
    }

    public function setMatiere(?Matiere $matiere): static
    {
        $this->matiere = $matiere;

        return $this;
    }

    
    public function getMois(): ?Mois
    {
        return $this->mois;
    }

    public function setMois(?Mois $mois): static
    {
        $this->mois = $mois;

        return $this;
    }
}

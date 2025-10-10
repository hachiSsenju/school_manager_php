<?php

namespace App\Entity;

use App\Repository\GradeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GradeRepository::class)]
class Grade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $note = null;

    #[ORM\Column]
    private ?int $note_maximal = null;

    #[ORM\Column(length: 255)]
    private ?string $type_examen = null;

    #[ORM\Column(length: 255)]
    private ?string $date = null;

    #[ORM\ManyToOne(inversedBy: 'grades')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Eleve $eleve = null;

    #[ORM\ManyToOne(inversedBy: 'grades')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Matiere $matiere = null;

    #[ORM\ManyToOne(inversedBy: 'grades')]
    private ?Bulletin $bulletin = null;

    #[ORM\ManyToOne(inversedBy: 'grades')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trimester $trimester = null;

   
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

    public function getNoteMaximal(): ?int
    {
        return $this->note_maximal;
    }

    public function setNoteMaximal(int $note_maximal): static
    {
        $this->note_maximal = $note_maximal;

        return $this;
    }

    public function getTypeExamen(): ?string
    {
        return $this->type_examen;
    }

    public function setTypeExamen(string $type_examen): static
    {
        $this->type_examen = $type_examen;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getEleve(): ?Eleve
    {
        return $this->eleve;
    }

    public function setEleve(?Eleve $eleve): static
    {
        $this->eleve = $eleve;

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

    public function getBulletin(): ?Bulletin
    {
        return $this->bulletin;
    }

    public function setBulletin(?Bulletin $bulletin): static
    {
        $this->bulletin = $bulletin;

        return $this;
    }

    public function getTrimester(): ?Trimester
    {
        return $this->trimester;
    }

    public function setTrimester(?Trimester $trimester): static
    {
        $this->trimester = $trimester;

        return $this;
    }

  
}

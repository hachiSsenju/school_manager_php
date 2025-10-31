<?php

namespace App\Entity;

use App\Repository\MatiereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatiereRepository::class)]
class Matiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $coefficient = null;

    #[ORM\ManyToOne(inversedBy: 'matieres')]
    private ?Professeur $professeur = null;

    #[ORM\ManyToOne(inversedBy: 'matieres')]
    private ?Classe $classe = null;



    #[ORM\ManyToOne(inversedBy: 'matiere')]
    private ?Bulletin $bulletin = null;

    /**
     * @var Collection<int, GradeH>
     */
    #[ORM\OneToMany(targetEntity: GradeH::class, mappedBy: 'matiere')]
    private Collection $gradeHs;

    /**
     * @var Collection<int, GradeP>
     */
    #[ORM\OneToMany(targetEntity: GradeP::class, mappedBy: 'matiere', orphanRemoval: true)]
    private Collection $gradePs;
    public function __construct()
    {
        $this->gradeHs = new ArrayCollection();
        $this->gradePs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCoefficient(): ?int
    {
        return $this->coefficient;
    }

    public function setCoefficient(int $coefficient): static
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    public function getProfesseur(): ?Professeur
    {
        return $this->professeur;
    }

    public function setProfesseur(?Professeur $professeur): static
    {
        $this->professeur = $professeur;

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): static
    {
        $this->classe = $classe;

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

    /**
     * @return Collection<int, GradeH>
     */
    public function getGradeHs(): Collection
    {
        return $this->gradeHs;
    }

    public function addGradeH(GradeH $gradeH): static
    {
        if (!$this->gradeHs->contains($gradeH)) {
            $this->gradeHs->add($gradeH);
            $gradeH->setMatiere($this);
        }

        return $this;
    }

    public function removeGradeH(GradeH $gradeH): static
    {
        if ($this->gradeHs->removeElement($gradeH)) {
            // set the owning side to null (unless already changed)
            if ($gradeH->getMatiere() === $this) {
                $gradeH->setMatiere(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GradeP>
     */
    public function getGradePs(): Collection
    {
        return $this->gradePs;
    }

    public function addGradeP(GradeP $gradeP): static
    {
        if (!$this->gradePs->contains($gradeP)) {
            $this->gradePs->add($gradeP);
            $gradeP->setMatiere($this);
        }

        return $this;
    }

    public function removeGradeP(GradeP $gradeP): static
    {
        if ($this->gradePs->removeElement($gradeP)) {
            // set the owning side to null (unless already changed)
            if ($gradeP->getMatiere() === $this) {
                $gradeP->setMatiere(null);
            }
        }

        return $this;
    }

   
}

<?php

namespace App\Entity;

use App\Repository\TrimesterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrimesterRepository::class)]
class Trimester
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'trimesters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Classe $classe = null;

    /**
     * @var Collection<int, Bulletin>
     */
    #[ORM\OneToMany(targetEntity: Bulletin::class, mappedBy: 'trimester', orphanRemoval: true)]
    private Collection $bulletins;

    /**
     * @var Collection<int, Grade>
     */
    #[ORM\OneToMany(targetEntity: Grade::class, mappedBy: 'trimester', orphanRemoval: true)]
    private Collection $grades;

    /**
     * @var Collection<int, GradeH>
     */
    #[ORM\OneToMany(targetEntity: GradeH::class, mappedBy: 'trimester', orphanRemoval: true)]
    private Collection $gradeHs;

    /**
     * @var Collection<int, GradeP>
     */
    #[ORM\OneToMany(targetEntity: GradeP::class, mappedBy: 'Trimester', orphanRemoval: true)]
    private Collection $gradePs;

    /**
     * @var Collection<int, Cycle>
     */
    #[ORM\OneToMany(targetEntity: Cycle::class, mappedBy: 'Trimester')]
    private Collection $cycles;
    public function __construct()
    {
        $this->bulletins = new ArrayCollection();
        $this->grades = new ArrayCollection();
        $this->gradeHs = new ArrayCollection();
        $this->gradePs = new ArrayCollection();
        $this->cycles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

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

    /**
     * @return Collection<int, Bulletin>
     */
    public function getBulletins(): Collection
    {
        return $this->bulletins;
    }

    public function addBulletin(Bulletin $bulletin): static
    {
        if (!$this->bulletins->contains($bulletin)) {
            $this->bulletins->add($bulletin);
            $bulletin->setTrimester($this);
        }

        return $this;
    }

    public function removeBulletin(Bulletin $bulletin): static
    {
        if ($this->bulletins->removeElement($bulletin)) {
            // set the owning side to null (unless already changed)
            if ($bulletin->getTrimester() === $this) {
                $bulletin->setTrimester(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Grade>
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }

    public function addGrade(Grade $grade): static
    {
        if (!$this->grades->contains($grade)) {
            $this->grades->add($grade);
            $grade->setTrimester($this);
        }

        return $this;
    }

    public function removeGrade(Grade $grade): static
    {
        if ($this->grades->removeElement($grade)) {
            // set the owning side to null (unless already changed)
            if ($grade->getTrimester() === $this) {
                $grade->setTrimester(null);
            }
        }

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
            $gradeH->setTrimester($this);
        }

        return $this;
    }

    public function removeGradeH(GradeH $gradeH): static
    {
        if ($this->gradeHs->removeElement($gradeH)) {
            // set the owning side to null (unless already changed)
            if ($gradeH->getTrimester() === $this) {
                $gradeH->setTrimester(null);
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
            $gradeP->setTrimester($this);
        }

        return $this;
    }

    public function removeGradeP(GradeP $gradeP): static
    {
        if ($this->gradePs->removeElement($gradeP)) {
            // set the owning side to null (unless already changed)
            if ($gradeP->getTrimester() === $this) {
                $gradeP->setTrimester(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cycle>
     */
    public function getCycles(): Collection
    {
        return $this->cycles;
    }

    public function addCycle(Cycle $cycle): static
    {
        if (!$this->cycles->contains($cycle)) {
            $this->cycles->add($cycle);
            $cycle->setTrimester($this);
        }

        return $this;
    }

    public function removeCycle(Cycle $cycle): static
    {
        if ($this->cycles->removeElement($cycle)) {
            // set the owning side to null (unless already changed)
            if ($cycle->getTrimester() === $this) {
                $cycle->setTrimester(null);
            }
        }

        return $this;
    }

   
}

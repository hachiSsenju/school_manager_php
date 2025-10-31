<?php

namespace App\Entity;

use App\Repository\CycleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CycleRepository::class)]
class Cycle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, GradeH>
     */
    #[ORM\OneToMany(targetEntity: GradeH::class, mappedBy: 'cycle', orphanRemoval: true)]
    private Collection $Grade_Hs;

    #[ORM\ManyToOne(inversedBy: 'cycles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bulletin $Bulletin = null;


    /**
     * @var Collection<int, Moyenne>
     */
    #[ORM\OneToMany(targetEntity: Moyenne::class, mappedBy: 'cycle')]
    private Collection $moyennes;

    #[ORM\Column(nullable: true)]
    private ?float $moyenne = null;

    #[ORM\Column(nullable: true)]
    private ?int $rank = null;

    public function __construct()
    {
        $this->Grade_Hs = new ArrayCollection();
        $this->moyennes = new ArrayCollection();
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

    /**
     * @return Collection<int, GradeH>
     */
    public function getGradeHs(): Collection
    {
        return $this->Grade_Hs;
    }

    public function addGradeH(GradeH $gradeH): static
    {
        if (!$this->Grade_Hs->contains($gradeH)) {
            $this->Grade_Hs->add($gradeH);
            $gradeH->setCycle($this);
        }

        return $this;
    }

    public function removeGradeH(GradeH $gradeH): static
    {
        if ($this->Grade_Hs->removeElement($gradeH)) {
            // set the owning side to null (unless already changed)
            if ($gradeH->getCycle() === $this) {
                $gradeH->setCycle(null);
            }
        }

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



    /**
     * @return Collection<int, Moyenne>
     */
    public function getMoyennes(): Collection
    {
        return $this->moyennes;
    }

    public function addMoyenne(Moyenne $moyenne): static
    {
        if (!$this->moyennes->contains($moyenne)) {
            $this->moyennes->add($moyenne);
            $moyenne->setCycle($this);
        }

        return $this;
    }

    public function removeMoyenne(Moyenne $moyenne): static
    {
        if ($this->moyennes->removeElement($moyenne)) {
            // set the owning side to null (unless already changed)
            if ($moyenne->getCycle() === $this) {
                $moyenne->setCycle(null);
            }
        }

        return $this;
    }

    public function getMoyenne(): ?float
    {
        return $this->moyenne;
    }

    public function setMoyenne(?float $moyenne): static
    {
        $this->moyenne = $moyenne;

        return $this;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(?int $rank): static
    {
        $this->rank = $rank;

        return $this;
    }
}

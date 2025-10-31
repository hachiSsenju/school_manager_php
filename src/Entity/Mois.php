<?php

namespace App\Entity;

use App\Repository\MoisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MoisRepository::class)]
class Mois
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, GradeP>
     */
    #[ORM\OneToMany(targetEntity: GradeP::class, mappedBy: 'mois', orphanRemoval: true)]
    private Collection $gradeP;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'mois')]
    private ?Bulletin $bulletin = null;

    public function __construct()
    {
        $this->gradeP = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, GradeP>
     */
    public function getGradeP(): Collection
    {
        return $this->gradeP;
    }

    public function addGradeP(GradeP $gradeP): static
    {
        if (!$this->gradeP->contains($gradeP)) {
            $this->gradeP->add($gradeP);
            $gradeP->setMois($this);
        }

        return $this;
    }

    public function removeGradeP(GradeP $gradeP): static
    {
        if ($this->gradeP->removeElement($gradeP)) {
            // set the owning side to null (unless already changed)
            if ($gradeP->getMois() === $this) {
                $gradeP->setMois(null);
            }
        }

        return $this;
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

    public function getBulletin(): ?Bulletin
    {
        return $this->bulletin;
    }

    public function setBulletin(?Bulletin $bulletin): static
    {
        $this->bulletin = $bulletin;

        return $this;
    }
}

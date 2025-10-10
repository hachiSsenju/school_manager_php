<?php

namespace App\Entity;

use App\Repository\ClasseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClasseRepository::class)]
class Classe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $niveau = null;

    #[ORM\Column]
    private ?int $frais = null;

    #[ORM\Column]
    private ?int $nbMax = null;

    /**
     * @var Collection<int, Matiere>
     */
    #[ORM\OneToMany(targetEntity: Matiere::class, mappedBy: 'classe', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $matieres;

    /**
     * @var Collection<int, Eleve>
     */
    #[ORM\OneToMany(targetEntity: Eleve::class, mappedBy: 'classe')]
    private Collection $eleves;

    /**
     * @var Collection<int, Trimester>
     */
    #[ORM\OneToMany(targetEntity: Trimester::class, mappedBy: 'classe', orphanRemoval: true)]
    private Collection $trimesters;

    /**
     * @var Collection<int, Bulletin>
     */
    #[ORM\OneToMany(targetEntity: Bulletin::class, mappedBy: 'Classe', orphanRemoval: true)]
    private Collection $bulletins;

    #[ORM\ManyToOne(inversedBy: 'Classe')]
    private ?Ecole $ecole = null;

    public function __construct()
    {
        $this->matieres = new ArrayCollection();
        $this->eleves = new ArrayCollection();
        $this->trimesters = new ArrayCollection();
        $this->bulletins = new ArrayCollection();
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

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getFrais(): ?int
    {
        return $this->frais;
    }

    public function setFrais(int $frais): static
    {
        $this->frais = $frais;

        return $this;
    }

    public function getNbMax(): ?int
    {
        return $this->nbMax;
    }

    public function setNbMax(int $nbMax): static
    {
        $this->nbMax = $nbMax;

        return $this;
    }

    /**
     * @return Collection<int, Matiere>
     */
    public function getMatieres(): Collection
    {
        return $this->matieres;
    }

    public function addMatiere(Matiere $matiere): static
    {
        if (!$this->matieres->contains($matiere)) {
            $this->matieres->add($matiere);
            $matiere->setClasse($this);
        }

        return $this;
    }

    public function removeMatiere(Matiere $matiere): static
    {
        if ($this->matieres->removeElement($matiere)) {
            // set the owning side to null (unless already changed)
            if ($matiere->getClasse() === $this) {
                $matiere->setClasse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Eleve>
     */
    public function getEleves(): Collection
    {
        return $this->eleves;
    }

    public function addElefe(Eleve $elefe): static
    {
        if (!$this->eleves->contains($elefe)) {
            $this->eleves->add($elefe);
            $elefe->setClasse($this);
        }

        return $this;
    }

    public function removeElefe(Eleve $elefe): static
    {
        if ($this->eleves->removeElement($elefe)) {
            // set the owning side to null (unless already changed)
            if ($elefe->getClasse() === $this) {
                $elefe->setClasse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trimester>
     */
    public function getTrimesters(): Collection
    {
        return $this->trimesters;
    }

    public function addTrimester(Trimester $trimester): static
    {
        if (!$this->trimesters->contains($trimester)) {
            $this->trimesters->add($trimester);
            $trimester->setClasse($this);
        }

        return $this;
    }

    public function removeTrimester(Trimester $trimester): static
    {
        if ($this->trimesters->removeElement($trimester)) {
            // set the owning side to null (unless already changed)
            if ($trimester->getClasse() === $this) {
                $trimester->setClasse(null);
            }
        }

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
            $bulletin->setClasse($this);
        }

        return $this;
    }

    public function removeBulletin(Bulletin $bulletin): static
    {
        if ($this->bulletins->removeElement($bulletin)) {
            // set the owning side to null (unless already changed)
            if ($bulletin->getClasse() === $this) {
                $bulletin->setClasse(null);
            }
        }

        return $this;
    }

    public function getEcole(): ?Ecole
    {
        return $this->ecole;
    }

    public function setEcole(?Ecole $ecole): static
    {
        $this->ecole = $ecole;

        return $this;
    }
}

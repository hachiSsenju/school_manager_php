<?php

namespace App\Entity;

use App\Repository\EleveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EleveRepository::class)]
class Eleve
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $birthday = null;

    #[ORM\Column]
    private ?int $solde_initial = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_parent = null;

    #[ORM\ManyToOne(inversedBy: 'eleves')]
    private ?Classe $classe = null;

    /**
     * @var Collection<int, Grade>
     */
    #[ORM\OneToMany(targetEntity: Grade::class, mappedBy: 'eleve', orphanRemoval: true)]
    private Collection $grades;

    /**
     * @var Collection<int, Bulletin>
     */
    #[ORM\OneToMany(targetEntity: Bulletin::class, mappedBy: 'eleve', orphanRemoval: true)]
    private Collection $bulletins;

    #[ORM\ManyToOne(inversedBy: 'eleves')]
    private ?Ecole $ecole = null;

    public function __construct()
    {
        $this->grades = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    public function setBirthday(string $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getSoldeInitial(): ?int
    {
        return $this->solde_initial;
    }

    public function setSoldeInitial(int $solde_initial): static
    {
        $this->solde_initial = $solde_initial;

        return $this;
    }

    public function getEmailParent(): ?string
    {
        return $this->email_parent;
    }

    public function setEmailParent(?string $email_parent): static
    {
        $this->email_parent = $email_parent;

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
     * @return Collection<int, Grade>
     */

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
            $grade->setEleve($this);
        }

        return $this;
    }

    public function removeGrade(Grade $grade): static
    {
        if ($this->grades->removeElement($grade)) {
            // set the owning side to null (unless already changed)
            if ($grade->getEleve() === $this) {
                $grade->setEleve(null);
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
            $bulletin->setEleve($this);
        }

        return $this;
    }

    public function removeBulletin(Bulletin $bulletin): static
    {
        if ($this->bulletins->removeElement($bulletin)) {
            // set the owning side to null (unless already changed)
            if ($bulletin->getEleve() === $this) {
                $bulletin->setEleve(null);
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

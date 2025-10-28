<?php

namespace App\Entity;

use App\Repository\BulletinRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BulletinRepository::class)]
class Bulletin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bulletins')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Eleve $eleve = null;

    #[ORM\ManyToOne(inversedBy: 'bulletins')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Classe $Classe = null;

    #[ORM\ManyToOne(inversedBy: 'bulletins')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trimester $trimester = null;

    
    #[ORM\Column]
    private ?bool $redoublant = null;

    #[ORM\Column(length: 255)]
    private ?string $annee_scholaire = null;

    #[ORM\ManyToOne(inversedBy: 'bulletins')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ecole $ecole = null;

    #[ORM\Column(length: 255)]
    private ?string $mention = null;

    #[ORM\Column(length: 255)]
    private ?string $rang = null;

    #[ORM\Column(length: 255)]
    private ?string $moy_annuelle = null;

    #[ORM\Column(length: 255)]
    private ?string $heure_absence = null;

    #[ORM\Column(length: 255)]
    private ?string $date = null;

    /**
     * @var Collection<int, Matiere>
     */
    #[ORM\OneToMany(targetEntity: Matiere::class, mappedBy: 'bulletin')]
    private Collection $matiere;

    /**
     * @var Collection<int, GradeP>
     */
    #[ORM\OneToMany(targetEntity: GradeP::class, mappedBy: 'Bulletin', orphanRemoval: true)]
    private Collection $gradePs;

    /**
     * @var Collection<int, Cycle>
     */
    #[ORM\OneToMany(targetEntity: Cycle::class,cascade: ["persist"], mappedBy: 'Bulletin', orphanRemoval: true)]
    private Collection $cycles;

    /**
     * @var Collection<int, GradeH>
     */
    #[ORM\OneToMany(targetEntity: GradeH::class, mappedBy: 'bulletin')]
    private Collection $gradeHs;

    public function __construct()
    {
        $this->matiere = new ArrayCollection();
        $this->gradePs = new ArrayCollection();
        $this->cycles = new ArrayCollection();
        $this->gradeHs = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
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

    public function getClasse(): ?Classe
    {
        return $this->Classe;
    }

    public function setClasse(?Classe $Classe): static
    {
        $this->Classe = $Classe;

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

    public function isRedoublant(): ?bool
    {
        return $this->redoublant;
    }

    public function setRedoublant(bool $redoublant): static
    {
        $this->redoublant = $redoublant;

        return $this;
    }

    public function getAnneeScholaire(): ?string
    {
        return $this->annee_scholaire;
    }

    public function setAnneeScholaire(string $annee_scholaire): static
    {
        $this->annee_scholaire = $annee_scholaire;

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

    public function getMention(): ?string
    {
        return $this->mention;
    }

    public function setMention(string $mention): static
    {
        $this->mention = $mention;

        return $this;
    }

    public function getRang(): ?string
    {
        return $this->rang;
    }

    public function setRang(string $rang): static
    {
        $this->rang = $rang;

        return $this;
    }

    public function getMoyAnnuelle(): ?string
    {
        return $this->moy_annuelle;
    }

    public function setMoyAnnuelle(string $moy_annuelle): static
    {
        $this->moy_annuelle = $moy_annuelle;

        return $this;
    }

    public function getHeureAbsence(): ?string
    {
        return $this->heure_absence;
    }

    public function setHeureAbsence(string $heure_absence): static
    {
        $this->heure_absence = $heure_absence;

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

    /**
     * @return Collection<int, Matiere>
     */
    public function getMatiere(): Collection
    {
        return $this->matiere;
    }

    public function addMatiere(Matiere $matiere): static
    {
        if (!$this->matiere->contains($matiere)) {
            $this->matiere->add($matiere);
            $matiere->setBulletin($this);
        }

        return $this;
    }

    public function removeMatiere(Matiere $matiere): static
    {
        if ($this->matiere->removeElement($matiere)) {
            // set the owning side to null (unless already changed)
            if ($matiere->getBulletin() === $this) {
                $matiere->setBulletin(null);
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
            $gradeP->setBulletin($this);
        }

        return $this;
    }

    public function removeGradeP(GradeP $gradeP): static
    {
        if ($this->gradePs->removeElement($gradeP)) {
            // set the owning side to null (unless already changed)
            if ($gradeP->getBulletin() === $this) {
                $gradeP->setBulletin(null);
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
            $cycle->setBulletin($this);
        }

        return $this;
    }

    public function removeCycle(Cycle $cycle): static
    {
        if ($this->cycles->removeElement($cycle)) {
            // set the owning side to null (unless already changed)
            if ($cycle->getBulletin() === $this) {
                $cycle->setBulletin(null);
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
            $gradeH->setBulletin($this);
        }

        return $this;
    }

    public function removeGradeH(GradeH $gradeH): static
    {
        if ($this->gradeHs->removeElement($gradeH)) {
            // set the owning side to null (unless already changed)
            if ($gradeH->getBulletin() === $this) {
                $gradeH->setBulletin(null);
            }
        }

        return $this;
    }

  
}

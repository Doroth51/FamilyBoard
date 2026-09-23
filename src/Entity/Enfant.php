<?php

namespace App\Entity;

use App\Repository\EnfantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnfantRepository::class)]
class Enfant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $classe = null;

    /**
     * @var Collection<int, FamilyGroup>
     */
    #[ORM\ManyToMany(targetEntity: FamilyGroup::class, inversedBy: 'enfants')]
    private Collection $familyGroup;

    /**
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'enfant')]
    private Collection $notes;

    /**
     * @var Collection<int, Remuneration>
     */
    #[ORM\OneToMany(targetEntity: Remuneration::class, mappedBy: 'enfant')]
    private Collection $remunerations;

    /**
     * @var Collection<int, Evaluation>
     */
    #[ORM\OneToMany(targetEntity: Evaluation::class, mappedBy: 'enfant')]
    private Collection $evaluations;

    public function __construct()
    {
        $this->familyGroup = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->remunerations = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getClasse(): ?string
    {
        return $this->classe;
    }

    public function setClasse(string $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * @return Collection<int, FamilyGroup>
     */
    public function getFamilyGroup(): Collection
    {
        return $this->familyGroup;
    }

    public function addFamilyGroup(FamilyGroup $familyGroup): self
    {
        if (!$this->familyGroup->contains($familyGroup)) {
            $this->familyGroup->add($familyGroup);
        }

        return $this;
    }

    public function removeFamilyGroup(FamilyGroup $familyGroup): self
    {
        $this->familyGroup->removeElement($familyGroup);

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setEnfant($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getEnfant() === $this) {
                $note->setEnfant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Remuneration>
     */
    public function getRemunerations(): Collection
    {
        return $this->remunerations;
    }

    public function addRemuneration(Remuneration $remuneration): self
    {
        if (!$this->remunerations->contains($remuneration)) {
            $this->remunerations->add($remuneration);
            $remuneration->setEnfant($this);
        }

        return $this;
    }

    public function removeRemuneration(Remuneration $remuneration): self
    {
        if ($this->remunerations->removeElement($remuneration)) {
            // set the owning side to null (unless already changed)
            if ($remuneration->getEnfant() === $this) {
                $remuneration->setEnfant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Evaluation>
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): self
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations->add($evaluation);
            $evaluation->setEnfant($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getEnfant() === $this) {
                $evaluation->setEnfant(null);
            }
        }

        return $this;
    }
}

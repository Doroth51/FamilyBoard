<?php

namespace App\Entity;

use App\Repository\PeriodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PeriodeRepository::class)]
class Periode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $numero = null;

    #[ORM\Column(length: 255)]
    private ?string $annee = null;

    /**
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'periode')]
    private Collection $notes;

    /**
     * @var Collection<int, Evaluation>
     */
    #[ORM\OneToMany(targetEntity: Evaluation::class, mappedBy: 'periode')]
    private Collection $enfant;

    #[ORM\ManyToOne(inversedBy: 'periode')]
    private ?PeriodeType $periodeType = null;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->enfant = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(string $annee): self
    {
        $this->annee = $annee;

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
            $note->setPeriode($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getPeriode() === $this) {
                $note->setPeriode(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Evaluation>
     */
    public function getEnfant(): Collection
    {
        return $this->enfant;
    }

    public function addEnfant(Evaluation $enfant): static
    {
        if (!$this->enfant->contains($enfant)) {
            $this->enfant->add($enfant);
            $enfant->setPeriode($this);
        }

        return $this;
    }

    public function removeEnfant(Evaluation $enfant): static
    {
        if ($this->enfant->removeElement($enfant)) {
            // set the owning side to null (unless already changed)
            if ($enfant->getPeriode() === $this) {
                $enfant->setPeriode(null);
            }
        }

        return $this;
    }

    public function getPeriodeType(): ?PeriodeType
    {
        return $this->periodeType;
    }

    public function setPeriodeType(?PeriodeType $periodeType): static
    {
        $this->periodeType = $periodeType;

        return $this;
    }
}

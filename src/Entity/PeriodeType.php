<?php

namespace App\Entity;

use App\Repository\PeriodeTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PeriodeTypeRepository::class)]
class PeriodeType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Periode>
     */
    #[ORM\OneToMany(targetEntity: Periode::class, mappedBy: 'periodeType')]
    private Collection $periode;

    public function __construct()
    {
        $this->periode = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Periode>
     */
    public function getPeriode(): Collection
    {
        return $this->periode;
    }

    public function addPeriode(Periode $periode): static
    {
        if (!$this->periode->contains($periode)) {
            $this->periode->add($periode);
            $periode->setPeriodeType($this);
        }

        return $this;
    }

    public function removePeriode(Periode $periode): static
    {
        if ($this->periode->removeElement($periode)) {
            // set the owning side to null (unless already changed)
            if ($periode->getPeriodeType() === $this) {
                $periode->setPeriodeType(null);
            }
        }

        return $this;
    }
}

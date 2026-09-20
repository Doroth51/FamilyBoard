<?php

namespace App\Entity;

use App\Repository\RemunerationTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RemunerationTypeRepository::class)]
class RemunerationType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Remuneration>
     */
    #[ORM\OneToMany(targetEntity: Remuneration::class, mappedBy: 'type')]
    private Collection $remunerations;

    public function __construct()
    {
        $this->remunerations = new ArrayCollection();
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
            $remuneration->setType($this);
        }

        return $this;
    }

    public function removeRemuneration(Remuneration $remuneration): self
    {
        if ($this->remunerations->removeElement($remuneration)) {
            // set the owning side to null (unless already changed)
            if ($remuneration->getType() === $this) {
                $remuneration->setType(null);
            }
        }

        return $this;
    }
}

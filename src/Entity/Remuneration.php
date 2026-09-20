<?php

namespace App\Entity;

use App\Repository\RemunerationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RemunerationRepository::class)]
class Remuneration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\ManyToOne(inversedBy: 'remunerations')]
    private ?RemunerationType $type = null;

    #[ORM\ManyToOne(inversedBy: 'remunerations')]
    private ?Enfant $enfant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getType(): ?RemunerationType
    {
        return $this->type;
    }

    public function setType(?RemunerationType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getEnfant(): ?Enfant
    {
        return $this->enfant;
    }

    public function setEnfant(?Enfant $enfant): static
    {
        $this->enfant = $enfant;

        return $this;
    }
}

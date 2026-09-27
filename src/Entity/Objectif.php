<?php

namespace App\Entity;

use App\Repository\ObjectifRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObjectifRepository::class)]
class Objectif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'objectifs')]
    private ?Enfant $enfant = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private int $progression = 0; // 0 à 100 %

    #[ORM\Column]
    private bool $termine = false;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getEnfant(): ?Enfant
    {
        return $this->enfant;
    }
    public function setEnfant(?Enfant $enfant): self
    {
        $this->enfant = $enfant;
        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }
    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getProgression(): int
    {
        return $this->progression;
    }
    public function setProgression(int $progression): self
    {
        $this->progression = $progression;
        return $this;
    }

    public function isTermine(): bool
    {
        return $this->termine;
    }
    public function setTermine(bool $termine): self
    {
        $this->termine = $termine;
        return $this;
    }
}

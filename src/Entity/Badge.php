<?php

namespace App\Entity;

use App\Repository\BadgeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BadgeRepository::class)]
class Badge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $nom;

    #[ORM\Column(length: 255)]
    private string $icone; // emoji ou icône

    #[ORM\Column(length: 255)]
    private string $condition; // ex: "moyenne>=15", "objectif_termine"

    #[ORM\ManyToOne(inversedBy: 'badges')]
    private ?Enfant $enfant = null;

    #[ORM\Column]
    private \DateTimeImmutable $dateObtention;

    public function __construct()
    {
        $this->dateObtention = new \DateTimeImmutable();
    }

    // getters / setters...

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of nom
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set the value of nom
     *
     * @return  self
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get the value of icone
     */
    public function getIcone()
    {
        return $this->icone;
    }

    /**
     * Set the value of icone
     *
     * @return  self
     */
    public function setIcone($icone)
    {
        $this->icone = $icone;

        return $this;
    }

    /**
     * Get the value of condition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Set the value of condition
     *
     * @return  self
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * Get the value of enfant
     */
    public function getEnfant()
    {
        return $this->enfant;
    }

    /**
     * Set the value of enfant
     *
     * @return  self
     */
    public function setEnfant($enfant)
    {
        $this->enfant = $enfant;

        return $this;
    }

    /**
     * Get the value of dateObtention
     */
    public function getDateObtention()
    {
        return $this->dateObtention;
    }

    /**
     * Set the value of dateObtention
     *
     * @return  self
     */
    public function setDateObtention($dateObtention)
    {
        $this->dateObtention = $dateObtention;

        return $this;
    }
}

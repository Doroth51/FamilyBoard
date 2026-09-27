<?php

namespace App\Service;

use App\Entity\Badge;
use App\Entity\Enfant;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;

class BadgeService
{
    private EntityManagerInterface $em;
    private NoteRepository $noteRepo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function checkBadges(Enfant $enfant): void
    {
        // Badge : moyenne >= 15
        $notes = $this->noteRepo->findBy(['enfant' => $enfant]);

        $moyenne = 0;
        if (count($notes) > 0) {
            $sum = 0;
            foreach ($notes as $note) {
                $sum += ($note->getNote() / $note->getDenominateur()) * 20;
            }
            $moyenne = $sum / count($notes);
        }

        if ($moyenne >= 15 && !$this->hasBadge($enfant, 'Excellent')) {
            $this->awardBadge($enfant, 'Excellent', '🏅');
        }

        // Badge : objectif terminé
        foreach ($enfant->getObjectifs() as $obj) {
            if ($obj->isTermine() && !$this->hasBadge($enfant, 'Objectif terminé')) {
                $this->awardBadge($enfant, 'Objectif terminé', '🎯');
            }
        }

        // Badge : 10 récompenses
        if (count($enfant->getRemunerations()) >= 10 && !$this->hasBadge($enfant, 'Super récompensé')) {
            $this->awardBadge($enfant, 'Super récompensé', '💎');
        }
    }

    private function hasBadge(Enfant $enfant, string $nom): bool
    {
        foreach ($enfant->getBadges() as $badge) {
            if ($badge->getNom() === $nom) return true;
        }
        return false;
    }

    private function awardBadge(Enfant $enfant, string $nom, string $icone): void
    {
        $badge = new Badge();
        $badge->setNom($nom);
        $badge->setIcone($icone);
        $badge->setEnfant($enfant);

        $this->em->persist($badge);
        $this->em->flush();
    }
}

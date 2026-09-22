<?php

namespace App\Form;

use App\Entity\Evaluation;
use App\Entity\Enfant;
use App\Entity\Periode;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('matiere', TextType::class, [
                'label' => 'Matière'
            ])
            ->add('niveau', ChoiceType::class, [
                'label' => 'Niveau de maîtrise',
                'choices' => [
                    'Très Bonne Maîtrise' => 'TBM',
                    'Maîtrise Suffisante' => 'MS',
                    'Maîtrise Insuffisante' => 'MI',
                    'Compétence Non Acquise' => 'CNA',
                ]
            ])
            ->add('libelle', TextType::class, [
                'required' => false,
                'label' => 'Commentaire'
            ])
            ->add('date', null, [
                'widget' => 'single_text',
                'label' => 'Date'
            ])
            ->add('enfant', EntityType::class, [
                'class' => Enfant::class,
                'choice_label' => 'name',
                'label' => 'Enfant'
            ])
            ->add('periode', EntityType::class, [
                'class' => Periode::class,
                'choice_label' => fn($p) => $p->getType() . ' ' . $p->getNumero() . ' ' . $p->getAnnee(),
                'label' => 'Période'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evaluation::class,
        ]);
    }
}

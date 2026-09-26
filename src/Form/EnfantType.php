<?php

namespace App\Form;

use App\Entity\Enfant;
use App\Entity\Classe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnfantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l’enfant'
            ])
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir une classe',
                'required' => true,
            ])
            ->add('evaluationMode', ChoiceType::class, [
                'label' => 'Mode d’évaluation',
                'choices' => [
                    'Notes chiffrées' => 'note',
                    'Évaluations par niveau de maîtrise' => 'evaluation',
                ],
            ])
            ->add('periodeType', ChoiceType::class, [
                'label' => 'Type de période',
                'choices' => [
                    'Semestre' => 'semestre',
                    'Trimestre' => 'trimestre',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enfant::class,
        ]);
    }
}

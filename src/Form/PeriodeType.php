<?php

namespace App\Form;

use App\Entity\Periode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Trimestre' => 'TRIMESTRE',
                    'Semestre' => 'SEMESTRE',
                ],
                'label' => 'Type'
            ])
            ->add('numero', IntegerType::class, [
                'label' => 'Numéro'
            ])
            ->add('annee', IntegerType::class, [
                'label' => 'Année scolaire'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Periode::class,
        ]);
    }
}

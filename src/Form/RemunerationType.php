<?php

namespace App\Form;

use App\Entity\Remuneration;
use App\Entity\Enfant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemunerationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant', MoneyType::class, [
                'label' => 'Montant'
            ])
            ->add('date', null, [
                'widget' => 'single_text',
                'label' => 'Date'
            ])
            ->add('type', EntityType::class, [
                'class' => RemunerationType::class,
                'choice_label' => 'name',
                'label' => 'Type'
            ])
            ->add('enfant', EntityType::class, [
                'class' => Enfant::class,
                'choice_label' => 'name',
                'label' => 'Enfant'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Remuneration::class,
        ]);
    }
}

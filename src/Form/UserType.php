<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\FamilyGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse email'
            ])

            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Parent' => 'ROLE_PARENT',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'expanded' => true,
                'multiple' => true,
            ])

            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => false,
                'mapped' => true,
            ])

            ->add('familyGroups', EntityType::class, [
                'class' => FamilyGroup::class,
                'choice_label' => 'name',
                'label' => 'Groupes familiaux',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

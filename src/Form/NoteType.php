<?php

namespace App\Form;

use App\Entity\Note;
use App\Entity\Matiere;
use App\Entity\Periode;
use App\Repository\PeriodeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $enfant = $options['enfant'];

        $builder
            ->add('enfantId', HiddenType::class, [
                'mapped' => false,
                'data' => $enfant->getId(),
            ])
            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => 'name',
                'label' => 'Matière'
            ])
            ->add('libelle', TextareaType::class, [
                'label' => 'Libelle',
                'required' => false,
            ])
            ->add('note', NumberType::class, [
                'label' => 'Note'
            ])
            ->add('denominateur', IntegerType::class, [
                'label' => 'Sur'
            ])
            ->add('coefficient', IntegerType::class, [
                'label' => 'Coefficient'
            ])
            ->add('isRemuneration', CheckboxType::class, [
                'label'    => 'Rémunération prévue ?',
                'required' => false,
            ])
            ->add('date', null, [
                'widget' => 'single_text',
                'label' => 'Date'
            ])
            ->add('periode', EntityType::class, [
                'class' => Periode::class,
                'choice_label' => fn(Periode $p) => $p->getType() . ' ' . $p->getNumero(),
                'query_builder' => function (PeriodeRepository $repo) use ($enfant) {
                    return $repo->createQueryBuilder('p')
                        ->where('p.type = :type')
                        ->setParameter('type', $enfant->getPeriodeType())
                        ->orderBy('p.numero', 'ASC');
                },
                'label' => 'Période'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
            'enfant' => null
        ]);
    }
}

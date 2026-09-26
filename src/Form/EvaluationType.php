<?php

namespace App\Form;

use App\Entity\Evaluation;
use App\Entity\Matiere;
use App\Entity\Periode;
use App\Repository\PeriodeRepository;
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
        $enfant = $options['enfant'];

        $builder
            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choice_label' => 'name',
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
            'data_class' => Evaluation::class,
            'enfant' => null,
        ]);
    }
}

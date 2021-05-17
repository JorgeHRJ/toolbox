<?php

namespace App\Form;

use App\Entity\CyclistRace;
use App\Form\Type\EditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CyclistRaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', EditorType::class, [
                'required' => true,
                'label' => 'Tus notas',
                'attr' => [
                    'placeholder' =>
                        'Redacta aquí los comentarios que desees sobre el ciclista para esta carrera.'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CyclistRace::class,
        ]);
    }
}

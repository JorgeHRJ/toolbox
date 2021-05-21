<?php

namespace App\Form;

use App\Entity\StageUser;
use App\Form\Type\EditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StageUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', EditorType::class, [
                'required' => true,
                'label' => 'Tus notas',
                'attr' => [
                    'placeholder' =>
                        'Redacta aquí los comentarios que desees sobre esta etapa de la carrera.'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StageUser::class,
        ]);
    }
}

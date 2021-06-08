<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Email *',
                'attr' => ['placeholder' => 'Email del usuario']
            ])
            ->add('password', PasswordType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'Contraseña',
                'attr' => ['placeholder' => 'Si se deja vacío, se crea una aleatoriamente']
            ])
            ->add('status', ChoiceType::class, [
                'required' => true,
                'label' => 'Estado',
                'choices' => [
                    'Deshabilitado' => User::DISABLED_STATUS,
                    'Habilitado' => User::ENABLED_STATUS,
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'required' => true,
                'label' => 'Roles *',
                'expanded' => true,
                'multiple' => true,
                'choice_attr' => function () {
                    return ['class' => 'form-check-input ml-3 mr-1'];
                },
                'choices' => [
                    'Administrador' => User::ROLE_ADMIN,
                    'Semanal' => User::ROLE_TASK,
                    'Cronos' => User::ROLE_CRONOS,
                    'Monedero' => User::ROLE_TRANSACTION,
                    'Balsas' => User::ROLE_RESERVOIR,
                    'Libro de Ruta' => User::ROLE_RACEBOOK,
                    'Recomendaciones de Riego' => User::ROLE_IRRIGATION
                ]
            ])
            ->add('reportable', CheckboxType::class, [
                'required' => false,
                'label' => '¿Debe recibir notificaciones este usuario?'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

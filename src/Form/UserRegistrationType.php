<?php

namespace App\Form;

use App\Entity\User;
use App\Library\Utils\Stringify;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegistrationType extends AbstractType
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

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
                    'Monedero' => User::ROLE_TRANSACTION,
                    'Balsas' => User::ROLE_RESERVOIR
                ]
            ])
        ;

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();

                /** @var User $user */
                $user = $event->getForm()->getData();
                if (empty($user->getPassword())) {
                    $password = $form->get('password')->getData() !== null
                        ? $form->get('password')->getData()
                        : Stringify::randomStr(12);
                    $password = $this->encoder->encodePassword($user, $password);
                    $user->setPassword($password);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

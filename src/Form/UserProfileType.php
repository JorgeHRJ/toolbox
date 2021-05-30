<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserProfileType extends AbstractType
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
            ->add('currentPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Contraseña actual',
                'attr' => ['placeholder' => 'Contraseña actual']
            ])
            ->add('newPassword', RepeatedType::class, [
                    'mapped' => false,
                    'type' => PasswordType::class,
                    'first_options' => [
                        'label' => 'Nueva contraseña',
                        'attr' => ['placeholder' => 'Nueva contraseña'],
                    ],
                    'second_options' => [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'Confirma tu nueva contraseña'
                        ],
                    ],
                    'required' => false,
                    'invalid_message' => 'Las contraseñas nuevas deben coincidir.'
            ])
            ->add('reportable', CheckboxType::class, [
                'required' => false,
                'label' => '¿Deseas recibir notificaciones via email de tus herramientas?'
            ])
        ;

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var User $user */
                $user = $event->getForm()->getData();

                $newPassword = $event->getForm()->get('newPassword')->getData();
                if ($newPassword !== null) {
                    $currentPassword = $event->getForm()->get('currentPassword')->getData();
                    if (!$this->encoder->isPasswordValid($user, $currentPassword)) {
                        $event->getForm()->addError(new FormError('La contraseña actual no es correcta.'));
                        return;
                    }

                    if (strlen($newPassword) < 12) {
                        $event->getForm()->addError(new FormError('La contraseña debe ser mayor a 12 caracteres.'));
                        return;
                    }

                    $newPassword = $this->encoder->encodePassword($user, $newPassword);
                    $user->setPassword($newPassword);
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

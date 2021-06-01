<?php

namespace App\Form;

use App\Entity\CronoClient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CronoClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Nombre *',
                'attr' => ['placeholder' => 'Nombre del cliente']
            ])
            ->add('color', HiddenType::class, [
                'required' => true
            ])
        ;

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $color = $event->getForm()->get('color')->getData();
                if (empty($color)) {
                    $event->getForm()->addError(new FormError('Se ha de seleccionar un color para el cliente.'));
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CronoClient::class,
        ]);
    }
}

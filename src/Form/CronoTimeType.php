<?php

namespace App\Form;

use App\Entity\CronoClient;
use App\Entity\CronoTime;
use App\Entity\User;
use App\Form\Type\DateTimePickerType;
use App\Form\Type\MarkdownEditorType;
use App\Service\CronoClientService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CronoTimeType extends AbstractType
{
    private CronoClientService $clientService;

    public function __construct(CronoClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $options['user'];
        /** @var CronoTime $time */
        $time = $builder->getData();

        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Título *',
                'attr' => ['placeholder' => 'Título']
            ])
            ->add('description', MarkdownEditorType::class, [
                'required' => false,
                'label' => 'Descripción'
            ])
            ->add('startAt', DateTimePickerType::class, [
                'required' => true,
                'label' => 'Comienzo *'
            ])
            ->add('endAt', DateTimePickerType::class, [
                'required' => true,
                'label' => 'Final *'
            ])
            ->add('client', ChoiceType::class, [
                'required' => true,
                'label' => 'Cliente *',
                'choices' => $this->clientService->getForChoices($user),
                'data' => $time->getClient() instanceof CronoClient ? $time->getClient()->getId() : null,
                'attr' => [
                    'data-component' => 'choices',
                    'data-type' => 'simple'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CronoTime::class,
            'user' => null
        ]);
    }
}

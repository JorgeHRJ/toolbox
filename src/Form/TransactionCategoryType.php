<?php

namespace App\Form;

use App\Entity\TransactionCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Título *',
                'attr' => ['placeholder' => 'Título de la categoría']
            ])
            ->add('periodicity', ChoiceType::class, [
                'required' => true,
                'label' => 'Peridiocidad *',
                'attr' => ['placeholder' => 'Peridiocidad de la categoría'],
                'choices' => [
                    'Ninguna' => TransactionCategory::NO_PERIDIOCITY,
                    'Mensual' => TransactionCategory::MONTHLY_PERIDIOCITY
                ]
            ])
            ->add('type', ChoiceType::class, [
                'required' => true,
                'label' => 'Tipo *',
                'attr' => ['placeholder' => 'Tipo de la categoría'],
                'choices' => [
                    'Gasto' => TransactionCategory::EXPENSE_TYPE,
                    'Ingreso' => TransactionCategory::INCOME_TYPE
                ]
            ])
            ->add('date', TextType::class, [
                'required' => true,
                'label' => 'Fecha (mes-año) *',
                'mapped' => false,
                'help' => 'En caso de tener peridiocidad mensual, se irá creando a partir de ese mes'
            ])
            ->add('month', TransactionMonthType::class, [
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TransactionCategory::class,
        ]);
    }
}

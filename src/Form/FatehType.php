<?php

namespace App\Form;

use App\Entity\Fateh;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FatehType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('title')
            ->add('address', TextareaType::class, [
                'required' => false,
            ])
            ->add('interests', TextareaType::class, [
                'required' => false,
            ])
            ->add('mealPreference')
            ->add('newsletterConsent', CheckboxType::class, [
                'required' => false,
            ])
            ->add('dataProtectionConsent', CheckboxType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fateh::class,
        ]);
    }
}

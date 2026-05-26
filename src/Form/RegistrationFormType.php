<?php

namespace App\Form;

use App\Entity\Fateh;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'First name',
                'constraints' => [
                    new NotBlank(message: 'Please enter your first name.'),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last name',
                'constraints' => [
                    new NotBlank(message: 'Please enter your last name.'),
                ],
            ])
            ->add('title', ChoiceType::class, [
                'label' => 'Title',
                'placeholder' => 'Select title',
                'required' => false,
                'choices' => [
                    'Mr.' => 'Mr.',
                    'Ms.' => 'Ms.',
                    'Dr.' => 'Dr.',
                    'Prof.' => 'Prof.',
                ],
            ])
            ->add('mealPreference', ChoiceType::class, [
                'label' => 'Meal preference',
                'placeholder' => 'Select meal preference',
                'required' => false,
                'choices' => [
                    'Vegetarian' => 'vegetarian',
                    'Vegan' => 'vegan',
                    'Halal' => 'halal',
                    'No preference' => 'no_preference',
                ],
            ])
            ->add('email', null, [
                'label' => 'Email address',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                                'mapped' => false,
                'label' => 'I agree to the summit terms.',
                'constraints' => [
                    new IsTrue(
                        message: 'You should agree to our terms.',
                    ),
                ],
            ])
            ->add('newsletterConsent', CheckboxType::class, [
                'label' => 'I would like to receive summit updates.',
                'required' => false,
            ])
            ->add('dataProtectionConsent', CheckboxType::class, [
                'label' => 'I agree to the data protection policy.',
                'constraints' => [
                    new IsTrue(message: 'Please agree to the data protection policy.'),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter a password',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        max: 4096,
                    ),
                ],
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

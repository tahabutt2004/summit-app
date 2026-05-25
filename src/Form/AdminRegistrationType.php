<?php

namespace App\Form;

use App\Entity\Registration;
use App\Entity\SummitLocation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('summitLocation', EntityType::class, [
                'class' => SummitLocation::class,
                'choice_label' => static function (SummitLocation $summitLocation): string {
                    $eventDate = $summitLocation->getEventDate()?->format('Y-m-d H:i') ?? 'No date';

                    return sprintf('%s - %s (%s)', $summitLocation->getLocationName(), $summitLocation->getCity(), $eventDate);
                },
            ])
            ->add('mealPreference', null, [
                'required' => false,
            ])
            ->add('specialNeeds', TextareaType::class, [
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => Registration::STATUS_ACTIVE,
                    'Cancelled' => Registration::STATUS_CANCELLED,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Registration::class,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Taha;
use App\Entity\ProfileChangeLog;
use App\Form\TahaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig');
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Taha) {
            throw $this->createAccessDeniedException();
        }

        $profileFields = [
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'title' => $user->getTitle(),
            'address' => $user->getAddress(),
            'interests' => $user->getInterests(),
            'mealPreference' => $user->getMealPreference(),
            'newsletterConsent' => $user->isNewsletterConsent(),
            'dataProtectionConsent' => $user->isDataProtectionConsent(),
        ];

        $form = $this->createForm(TahaType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $changedAt = new \DateTimeImmutable();
            $newProfileFields = [
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'title' => $user->getTitle(),
                'address' => $user->getAddress(),
                'interests' => $user->getInterests(),
                'mealPreference' => $user->getMealPreference(),
                'newsletterConsent' => $user->isNewsletterConsent(),
                'dataProtectionConsent' => $user->isDataProtectionConsent(),
            ];

            foreach ($profileFields as $fieldName => $oldValue) {
                $newValue = $newProfileFields[$fieldName];

                if ($oldValue === $newValue) {
                    continue;
                }

                $changeLog = (new ProfileChangeLog())
                    ->setUser($user)
                    ->setFieldName($fieldName)
                    ->setOldValue($this->normalizeProfileLogValue($oldValue))
                    ->setNewValue($this->normalizeProfileLogValue($newValue))
                    ->setChangedAt($changedAt);

                $entityManager->persist($changeLog);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'profileForm' => $form,
        ]);
    }

    private function normalizeProfileLogValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return (string) $value;
    }
}

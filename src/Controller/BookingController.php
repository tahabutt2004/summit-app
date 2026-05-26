<?php

namespace App\Controller;

use App\Entity\Fateh;
use App\Entity\Registration;
use App\Form\RegistrationBookingType;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class BookingController extends AbstractController
{
    #[Route('/summit/register', name: 'app_summit_register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, RegistrationRepository $registrationRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Fateh) {
            throw $this->createAccessDeniedException();
        }

        $registration = new Registration();
        $registration->setUser($user);
        $registration->setMealPreference($user->getMealPreference());

        $form = $this->createForm(RegistrationBookingType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $summitLocation = $registration->getSummitLocation();
            $now = new \DateTimeImmutable();

            if ($summitLocation === null || !$summitLocation->isActive() || $summitLocation->getEventDate() < $now) {
                $this->addFlash('booking_error', 'Please choose an active upcoming summit location.');

                return $this->redirectToRoute('app_summit_register');
            }

            if ($registrationRepository->findActiveForUserAndSummitLocation($user, $summitLocation) !== null) {
                $this->addFlash('booking_error', 'You already have an active booking for this summit location.');

                return $this->redirectToRoute('app_my_bookings');
            }

            $activeBookings = $registrationRepository->countActiveForSummitLocation($summitLocation);
            if ($activeBookings >= (int) $summitLocation->getCapacity()) {
                $this->addFlash('booking_error', 'This summit location is already full.');

                return $this->redirectToRoute('app_summit_register');
            }

            $registration
                ->setStatus(Registration::STATUS_ACTIVE)
                ->setCreatedAt($now);

            $entityManager->persist($registration);
            $entityManager->flush();

            $this->addFlash('booking_success', 'Your summit booking has been confirmed.');

            return $this->redirectToRoute('app_my_bookings');
        }

        return $this->render('booking/register.html.twig', [
            'bookingForm' => $form,
        ]);
    }

    #[Route('/my-bookings', name: 'app_my_bookings', methods: ['GET'])]
    public function myBookings(RegistrationRepository $registrationRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Fateh) {
            throw $this->createAccessDeniedException();
        }

        $now = new \DateTimeImmutable();
        $bookings = $registrationRepository->findBookingsForUser($user);
        $upcomingBookings = [];
        $previousBookings = [];

        foreach ($bookings as $booking) {
            $eventDate = $booking->getSummitLocation()?->getEventDate();
            if ($eventDate !== null && $eventDate >= $now) {
                $upcomingBookings[] = $booking;
                continue;
            }

            $previousBookings[] = $booking;
        }

        return $this->render('booking/my_bookings.html.twig', [
            'upcomingBookings' => $upcomingBookings,
            'previousBookings' => $previousBookings,
        ]);
    }

    #[Route('/booking/{id}/cancel', name: 'app_booking_cancel', methods: ['POST'])]
    public function cancel(Request $request, Registration $registration, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Fateh || $registration->getUser()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('cancel_booking_' . $registration->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $eventDate = $registration->getSummitLocation()?->getEventDate();
        if ($eventDate === null || $eventDate < new \DateTimeImmutable()) {
            $this->addFlash('booking_error', 'Past bookings cannot be cancelled.');

            return $this->redirectToRoute('app_my_bookings');
        }

        if ($registration->getStatus() === Registration::STATUS_CANCELLED) {
            $this->addFlash('booking_error', 'This booking is already cancelled.');

            return $this->redirectToRoute('app_my_bookings');
        }

        $registration->setStatus(Registration::STATUS_CANCELLED);
        $entityManager->flush();

        $this->addFlash('booking_success', 'Your booking has been cancelled.');

        return $this->redirectToRoute('app_my_bookings');
    }
}

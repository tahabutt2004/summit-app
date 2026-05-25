<?php

namespace App\Controller\Admin;

use App\Entity\Registration;
use App\Form\AdminRegistrationType;
use App\Repository\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/registrations')]
#[IsGranted('ROLE_ADMIN')]
final class RegistrationController extends AbstractController
{
    #[Route('', name: 'admin_registration_index', methods: ['GET'])]
    public function index(Request $request, RegistrationRepository $registrationRepository): Response
    {
        $status = $request->query->getString('status');
        $city = $request->query->getString('city');
        $sort = $request->query->getString('sort', 'createdAt');
        $direction = $request->query->getString('direction', 'DESC');

        return $this->render('admin/registration/index.html.twig', [
            'registrations' => $registrationRepository->findForAdmin($status, $city, $sort, $direction),
            'cities' => $registrationRepository->findAdminCities(),
            'currentStatus' => $status,
            'currentCity' => $city,
            'currentSort' => $sort,
            'currentDirection' => strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC',
        ]);
    }

    #[Route('/export', name: 'admin_registration_export', methods: ['GET'])]
    public function export(Request $request, RegistrationRepository $registrationRepository): Response
    {
        $registrations = $registrationRepository->findForAdmin(
            $request->query->getString('status'),
            $request->query->getString('city'),
            $request->query->getString('sort', 'createdAt'),
            $request->query->getString('direction', 'DESC')
        );

        $rows = [
            ['ID', 'User Email', 'Summit City', 'Event Date', 'Meal Preference', 'Special Needs', 'Status', 'Created At'],
        ];

        foreach ($registrations as $registration) {
            $summitLocation = $registration->getSummitLocation();
            $rows[] = [
                $registration->getId(),
                $registration->getUser()?->getEmail(),
                $summitLocation?->getCity(),
                $summitLocation?->getEventDate()?->format('Y-m-d H:i'),
                $registration->getMealPreference(),
                $registration->getSpecialNeeds(),
                $registration->getStatus(),
                $registration->getCreatedAt()?->format('Y-m-d H:i'),
            ];
        }

        return new Response($this->buildExcelTable($rows), Response::HTTP_OK, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="summit-registrations.xls"',
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_registration_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Registration $registration, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminRegistrationType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_registration_index');
        }

        return $this->render('admin/registration/edit.html.twig', [
            'registration' => $registration,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_registration_delete', methods: ['POST'])]
    public function delete(Request $request, Registration $registration, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_registration_' . $registration->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($registration);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_registration_index');
    }

    /**
     * Creates an Excel-compatible HTML workbook without requiring PHP zip/gd extensions.
     *
     * @param list<list<mixed>> $rows
     */
    private function buildExcelTable(array $rows): string
    {
        $html = '<html><head><meta charset="UTF-8"></head><body><table border="1">';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $value) {
                $html .= '<td>' . htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table></body></html>';

        return $html;
    }
}

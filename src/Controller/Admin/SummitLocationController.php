<?php

namespace App\Controller\Admin;

use App\Entity\SummitLocation;
use App\Form\SummitLocationType;
use App\Repository\SummitLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/summit-location')]
#[IsGranted('ROLE_ADMIN')]
final class SummitLocationController extends AbstractController
{
    #[Route('', name: 'admin_summit_location_index', methods: ['GET'])]
    public function index(SummitLocationRepository $summitLocationRepository): Response
    {
        return $this->render('admin/summit_location/index.html.twig', [
            'summitLocations' => $summitLocationRepository->findBy([], ['eventDate' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'admin_summit_location_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $summitLocation = new SummitLocation();
        $summitLocation->setIsActive(true);

        $form = $this->createForm(SummitLocationType::class, $summitLocation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($summitLocation);
            $entityManager->flush();

            return $this->redirectToRoute('admin_summit_location_index');
        }

        return $this->render('admin/summit_location/new.html.twig', [
            'summitLocation' => $summitLocation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_summit_location_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SummitLocation $summitLocation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SummitLocationType::class, $summitLocation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_summit_location_index');
        }

        return $this->render('admin/summit_location/edit.html.twig', [
            'summitLocation' => $summitLocation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_summit_location_delete', methods: ['POST'])]
    public function delete(Request $request, SummitLocation $summitLocation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_summit_location_' . $summitLocation->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($summitLocation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_summit_location_index');
    }
}

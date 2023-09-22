<?php

namespace App\Controller\Admin;

use App\Entity\SchoolYear;
use App\Form\SchoolYearType;
use App\Repository\SchoolYearRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/school-year')]
class SchoolYearController extends AbstractController
{
    #[Route('/', name: 'app_admin_school_year_index', methods: ['GET'])]
    public function index(SchoolYearRepository $schoolYearRepository): Response
    {
        return $this->render('admin/school_year/index.html.twig', [
            'school_years' => $schoolYearRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_school_year_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $schoolYear = new SchoolYear();
        $form = $this->createForm(SchoolYearType::class, $schoolYear);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($schoolYear);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_school_year_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/school_year/new.html.twig', [
            'school_year' => $schoolYear,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_school_year_show', methods: ['GET'])]
    public function show(SchoolYear $schoolYear): Response
    {
        return $this->render('admin/school_year/show.html.twig', [
            'school_year' => $schoolYear,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_school_year_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SchoolYear $schoolYear, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SchoolYearType::class, $schoolYear);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_school_year_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/school_year/edit.html.twig', [
            'school_year' => $schoolYear,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_school_year_delete', methods: ['POST'])]
    public function delete(Request $request, SchoolYear $schoolYear, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$schoolYear->getId(), $request->request->get('_token'))) {
            $entityManager->remove($schoolYear);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_school_year_index', [], Response::HTTP_SEE_OTHER);
    }
}

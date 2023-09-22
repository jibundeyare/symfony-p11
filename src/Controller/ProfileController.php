<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\StudentProfileType;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        if (!$this->isGranted('ROLE_ADMIN')) {
            // le session user n'est pas un admin

            /** @var \App\Entity\User $sessionUser */
            $sessionUser = $this->getUser();
            $student = $sessionUser->getStudent();
            $schoolYear = $student->getSchoolYear();
            $users = $userRepository->findBySchoolYear($schoolYear);
        }

        return $this->render('profile/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new', name: 'app_profile_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // if (!$this->isGranted('ROLE_ADMIN')) {
        //     throw new AccessDeniedException();
        // }

        // fait la même chose que le code ci-dessus
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $form = $this->createForm(StudentProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_profile_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        $this->filterSessionUser($user);

        return $this->render('profile/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->filterSessionUser($user);

        $form = $this->createForm(StudentProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/password', name: 'app_profile_password', methods: ['GET', 'POST'])]
    public function password(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->filterSessionUser($user);

        $form = $this->createForm(UserPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_profile_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // if (!$this->isGranted('ROLE_ADMIN')) {
        //     throw new AccessDeniedException();
        // }

        // fait la même chose que le code ci-dessus
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_profile_index', [], Response::HTTP_SEE_OTHER);
    }

    private function filterSessionUser(User $user)
    {
        $sessionUser = $this->getUser();

        if ($sessionUser != $user) {
            // le user connecté essaie de consulter le profil d'un autre user
            throw new AccessDeniedException();
        }
    }
}

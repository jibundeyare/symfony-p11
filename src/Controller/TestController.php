<?php

namespace App\Controller;

use Exception;
use App\Entity\Student;
use App\Entity\Tag;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/test')]
class TestController extends AbstractController
{
    #[Route('/tag', name: 'app_test_tag')]
    public function tag(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $studentRepository = $em->getRepository(Student::class);
        $tagRepository = $em->getRepository(Tag::class);

        // création d'un nouvel objet
        $foo = new Tag();
        $foo->setName('Foo');
        $foo->setDescription('Foo bar baz');
        $em->persist($foo);

        try {
            $em->flush();
        } catch (Exception $e) {
            // gérer l'erreur
            dump($e->getMessage());
        }

        // récupération de l'objet dont l'id est 1
        $tag = $tagRepository->find(1);

        // récupération de l'objet dont l'id est 15
        $tag15 = $tagRepository->find(15);

        // suppression de l'objet seulement s'il existe
        if ($tag15) {
            // suppression de l'objet
            $em->remove($tag15);
            $em->flush();
        }

        // récupération de l'objet dont l'id est 4
        $tag4 = $tagRepository->find(4);

        // modification d'un objet
        $tag4->setName('Python');
        $tag4->setDescription(null);
        // pas la peine d'appeler persist() si l'objet provient de la BDD
        $em->flush();

        // récupération du student dont l'id est 1
        $student = $studentRepository->find(1);

        // association du tag 4 au student 1
        $student->addTag($tag4);
        $em->flush();

        // récupération d'un tag dont le nom est CSS
        $cssTag = $tagRepository->findOneBy([
            // critères de recherche
            'name' => 'CSS',
        ]);

        // récupération de tous les tag dont la description est nulle
        $nullDescriptionTags = $tagRepository->findBy([
            // critères de recherche
            'description' => null,
        ], [
            // critères de tri
            'name' => 'ASC',
        ]);
        // ou
        $nullDescriptionTags = $tagRepository->findByNotNullDescription();

        // récupération de tous les tags avec description
        $notNullDescriptionTags = $tagRepository->findByNotNullDescription();

        // récupération de la liste complète des objets
        $tags = $tagRepository->findAll();

        $title = 'Test des tags';

        return $this->render('test/tag.html.twig', [
            'title' => $title,
            'tags' => $tags,
            'tag' => $tag,
            'cssTag' => $cssTag,
            'nullDescriptionTags' => $nullDescriptionTags,
            'notNullDescriptionTags' => $notNullDescriptionTags,
        ]);
    }

    #[Route('/school-year', name: 'app_test_schoolyear')]
    public function schoolYear(): Response
    {
        return $this->render('test/school-year.html.twig', [
            // ...
        ]);
    }
}

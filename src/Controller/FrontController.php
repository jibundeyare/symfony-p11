<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TagRepository $tagRespository): Response
    {
        // redirection vers la page de login
        // return $this->redirectToRoute('app_login');

        $tags = $tagRespository->findAll();

        return $this->render('front/index.html.twig', [
            'tags' => $tags,
        ]);
    }

    #[Route('/tag/{id}', name: 'app_front_tag_show')]
    public function tagShow(Tag $tag): Response
    {
        return $this->render('front/tag_show.html.twig', [
            'tag' => $tag,
        ]);
    }
}

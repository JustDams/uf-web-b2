<?php

namespace App\Controller;

use App\Entity\Games;
use App\Form\SearchFormType;
use App\Repository\GamesRepository\GamesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GamesController extends AbstractController
{
    /**
     * @Route("/{page}", name="index")
     */
    public function index(Request $request, $page = 1)
    {
        $games = $this->getDoctrine()
            ->getRepository(Games::class)
            ->findGames($page);

        $pagesLen = [];
        for ($i = $page + 1; $i < $page + 3; $i++) {
            $pageLen = $this->getDoctrine()
                ->getRepository(Games::class)
                ->findGames($i);
            array_push($pagesLen, count($pageLen));
        }

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        return $this->render('games/index.html.twig', [
            'games' => $games,
            'pagesLen' => $pagesLen,
            'page' => $page,
            'searchform' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search/{game}", name="search")
     */
    public function search(Request $request, $game)
    {
        $games = $this->getDoctrine()
            ->getRepository(Games::class)
            ->search($game);

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        return $this->render('games/search.html.twig', [
            'games' => $games,
            'research' => $game,
            'searchform' => $form->createView(),
        ]);
    }
}

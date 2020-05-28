<?php

namespace App\Controller;

use App\Entity\Games;
use App\Form\CommentsFormType;
use App\Form\SearchFormType;
use App\Repository\GamesRepository\GamesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class GamesController extends AbstractController
{
    /**
     * @Route("/", name="redirectIndex")
     */
    public function redirectIndex()
    {
        return $this->redirectToRoute('index', [
            'page' => 1
        ]);
    }

    /**
     * @Route("/index/{page}", name="index")
     */
    public function index(Request $request, $page = 1)
    {
        $user = $this->getUser();

        if ($page < 1) {
            return $this->redirectToRoute('index', ['page' => 1]);
        }

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
            'user' => $user,
            'games' => $games,
            'pagesLen' => $pagesLen,
            'page' => $page,
            'searchform' => $form->createView(),
        ]);
    }

    /**
     * @Route("/search/{game}", name="search")
     */
    public function search(Request $request, $game = null)
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

    /**
     * @Route("/game/{id}", name="game")
     */
    public function game(Request $request, $id)
    {
        if ($id == null || gettype($id) != 'string' ) {
            return $this->redirectToRoute('index');
        }

        $user = $this->getUser();

        $game = $this->getDoctrine()->getRepository(Games::class)->find($id);


        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        $commentForm = $this->createForm(CommentsFormType::class);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            return $this->redirectToRoute('game', ['id' => $id]);
        }

        return $this->render('games/game.html.twig', [
            'game' => $game,
            'user' => $user,
            'searchform' => $form->createView(),
            'commentForm' => $commentForm->createView(),
        ]);
    }
}

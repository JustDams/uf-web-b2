<?php

namespace App\Controller;

use App\Entity\Code;
use App\Entity\Comments;
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
        $user = $this->getUser();

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
            'user' => $user,
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
        if ($id == null || gettype($id) != 'string') {
            return $this->redirectToRoute('index');
        }

        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $canComment = null;

        if ($user != null) {
            $userId = $user->getId();
            $canComment = $this->getDoctrine()->getRepository(Code::class)->findOneBy([
                'idGame' => $id,
                'idUser' => $userId
            ]);
        }
        
        if ($canComment != null) {
            $canComment = True;
        } else {
            $canComment = False;
        }
        

        $game = $this->getDoctrine()->getRepository(Games::class)->find($id);
        $comments = $this->getDoctrine()->getRepository(Comments::class)->findBy([
            'idGame' => $id
        ]);
        $globalNote = Null;
        for ($i = 0; $i < count($comments); $i++) {
            $globalNote += $comments[$i]->getNote();
        }
        if (count($comments) > 0) {
            $globalNote = $globalNote / count($comments);
        }

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        $commentForm = $this->createForm(CommentsFormType::class);
        $commentForm->handleRequest($request);
        $comment = new Comments();

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setIdGame($this->getDoctrine()->getRepository(Games::class)->find($id));
            $comment->setTitle($commentForm->getData()->getTitle());
            $comment->setContent($commentForm->getData()->getContent());
            $comment->setNote($commentForm->getData()->getNote());
            $comment->setIdUser($user);
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('game', ['id' => $id]);
        }

        return $this->render('games/game.html.twig', [
            'game' => $game,
            'user' => $user,
            'globalNote' => $globalNote,
            'comments' => $comments,
            'canComment' => $canComment,
            'searchform' => $form->createView(),
            'commentForm' => $commentForm->createView(),
        ]);
    }

    /**
     * @Route("/type/{type}", name="type")
     */
    public function type(Request $request, $type)
    {
        $user = $this->getUser();

        $games = $this->getDoctrine()->getRepository(Games::class)->searchType($type);

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        return $this->render('games/type.html.twig', [
            'type' => $type,
            'games' => $games,
            'searchform' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/esport", name="esport")
     */
    public function esport(Request $request)
    {
        $user = $this->getUser();

        $games = $this->getDoctrine()->getRepository(Games::class)->searchEsport();

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        return $this->render('games/esport.html.twig', [
            'games' => $games,
            'searchform' => $form->createView(),
            'user' => $user,
        ]);
    }
}

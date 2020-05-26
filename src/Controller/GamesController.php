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

        if ($games == null) {
            throw $this->createNotFoundException(
                'Error connecting to database'
            );
        }

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        return $this->render('games/index.html.twig', [
            'games' => $games,
            'page' => $page,
            'searchform' => $form->createView(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Games;
use App\Repository\GamesRepository\GamesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GamesController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index($page = 1)
    {

        $games = $this->getDoctrine()
            ->getRepository(Games::class)
            ->find10First();

        if ($games == null) {
            throw $this->createNotFoundException(
                'Error connecting to database'
            );
        }

        return $this->render('games/index.html.twig', [
            'games' => $games
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GamesController extends AbstractController
{
    /**
     * @Route("/games", name="games")
     */
    public function index()
    {
        return $this->render('games/index.html.twig', [
            'controller_name' => 'GamesController',
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Games;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GamesController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {

        $games = $this->getDoctrine()
            ->getRepository(Games::class)
            ->find10First();

        if ($games == null) {
            throw $this->createNotFoundException(
                'Error connecting to database'
            );
        }

        // or render a template
        // in the template, print things with {{ product.name }}
        // return $this->render('product/show.html.twig', ['product' => $product]);


        return $this->render('games/index.html.twig', [
            'controller_name' => 'GamesController', 'games' => $games
        ]);
    }
}

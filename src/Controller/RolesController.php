<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RolesController extends AbstractController
{
    /**
     * @Route("/roles", name="roles")
     */
    public function index()
    {
        return $this->render('roles/index.html.twig', [
            'controller_name' => 'RolesController',
        ]);
    }
}

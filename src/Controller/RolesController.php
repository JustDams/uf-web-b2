<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RolesController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $user = $this->getUser();
        if ($user != null) {
            $role = $user->getRoles();
            if ($role != 'admin') {
                return $this->redirectToRoute('index');
            }
        } else {
            return $this->redirectToRoute('index');
        }

        return $this->render('roles/index.html.twig', [
            'role' => $role,
        ]);
    }
}

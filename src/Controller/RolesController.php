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
            $role= $user->getRoles();
            if ($role != 'ROLE_USER') {
                return $this->render('roles/index.html.twig', [
                    'role' => $role,
                ]);
            }
            else {
                return $role;
            }
        } else {
            return $user;
        }

        return $this->render('roles/index.html.twig', [
            'role' => $role,
        ]);
    }
}

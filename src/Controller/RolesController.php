<?php

namespace App\Controller;

use App\Entity\Roles;
use App\Form\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RolesController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

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
    }
}

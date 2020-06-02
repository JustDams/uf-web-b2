<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        $users = $this->getDoctrine()->getRepository(Users::class)->findAll();

        if ($user != null) {
            $role = $user->getRoles();
            if ($role != 'ROLE_USER') {
                return $this->render('admin/admin.html.twig', [
                    'user' => $user,
                    'role' => $role,
                    'users' => $users,
                    'searchform' => $searchForm->createView(),
                ]);
            } else {
                return $this->redirectToRoute('index');
            }
        } else {
            return $this->redirectToRoute('index');
        }
    }
}

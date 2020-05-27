<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\SearchFormType;
use App\Form\UsersFormType;
use Symfony\Component\HttpFoundation\Request;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function index(Request $request)
    {
        $user = new Users();
        $form = $this->createForm(UsersFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        return $this->render('users/index.html.twig', [
            'searchform' => $searchForm->createView(),
            'userForm' => $form->createView(),
        ]);
    }
}

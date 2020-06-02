<?php

namespace App\Controller;

use App\Entity\Code;
use App\Entity\Roles;
use App\Entity\Users;
use App\Form\SearchFormType;
use App\Form\UsersFormType;
use Symfony\Component\HttpFoundation\Request;
use http\Env\Response;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UsersController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        if ($this->getUser() != null) {
            return $this->redirectToRoute('index');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $user = new Users();

        $form = $this->createForm(UsersFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setBalance(0);
            $user->setRegisterDate(new \DateTime('now'));
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('login');
        }

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        return $this->render('users/register.html.twig', [
            'searchform' => $searchForm->createView(),
            'userForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $utils)
    {
        if ($this->getUser() != null) {
            return $this->redirectToRoute('index');
        }

        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        return $this->render('users/login.html.twig', [
            'searchform' => $searchForm->createView(),
            'error' => $error, 'last_username' => $lastUsername
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
    }

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
                return $this->render('users/admin.html.twig', [
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

    /**
     * @Route("/profile", name="profile")
     */
    public function profile(Request $request)
    {
        $user = $this->getUser();

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        $games = $this->getDoctrine()->getRepository(Code::class)->findBy([
            'idUser' => $user->getId()
        ]);

        return $this->render('users/profile.html.twig', [
            'user' => $user,
            'games' => $games,
            'searchform' => $searchForm->createView(),
        ]);
    }

     /**
     * @Route("/editUser/{id}", name="editUser")
     */
    public function editUser(Request $request, $id)
    {

    }
}

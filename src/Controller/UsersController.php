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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\User;
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

            
            $this->addFlash('success', 'Your account as been well created.');
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
     * @Route("/profile", name="profile")
     */
    public function profile(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($user == null) {
            return $this->redirectToRoute('index');
        }

        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class, ['required' => true])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                'required' => true
            ])
            ->add('register', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('profile');
        }

        $games = $this->getDoctrine()->getRepository(Code::class)->findBy([
            'idUser' => $user->getId(),
        ]);

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        return $this->render('users/profile.html.twig', [
            'user' => $user,
            'games' => $games,
            'userForm' => $form->createView(),
            'searchform' => $searchForm->createView(),
        ]);
    }

    /**
     * @Route("/editUser/{id}", name="editUser")
     */
    public function editUser(Request $request, Users $user, UserPasswordEncoderInterface $encoder)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        $form = $this->createForm(UsersFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('profile');
        }




        return $this->render('users/edit.html.twig', [
            'searchform' => $searchForm->createView(),
            'userForm' => $form->createView()
        ]);
    }
}

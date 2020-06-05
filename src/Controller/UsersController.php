<?php

namespace App\Controller;

use App\Entity\Code;
use App\Entity\Comments;
use App\Entity\Roles;
use App\Entity\Users;
use App\Form\SearchFormType;
use App\Form\UsersFormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use http\Env\Response;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
    public function editUser(Request $request, UserPasswordEncoderInterface $encoder, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(Users::class)->find($id);
        $comments = $this->getDoctrine()->getRepository(Comments::class)->findBy( ['idUser' => $user -> getId()]);

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class, ['required' => true])
            ->add('firstname', TextType::class, ['required' => true])
            ->add('lastname', TextType::class, ['required' => true])
            ->add('email', EmailType::class, ['required' => true])
            ->add('birthday', BirthdayType::class, ['required' => true])
            ->add('balance', MoneyType::class, ['required' => true])
            ->add('register', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('admin');
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'comments' => $comments,
            'searchform' => $searchForm->createView(),
            'userForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/removeUser/{id}", name="removeUser")
     */
    public function removeUser($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getDoctrine()->getRepository(Users::class)->find($id);
        if ($user == null) {
            $this->addFlash('errors', 'This user doesn\'t exist');
            return $this->redirectToRoute('admin');
        }
        $manager = $this->getDoctrine()->getManager();
        $cart = $user->getCarts();
        for ($i=0; $i < count($cart); $i++) {
            $stock = $cart[$i]->getIdGame()->getStock();
            $cart[$i]->getIdGame()->setStock($stock + 1);
        }

        $code = $user->getCodes();
        for ($i=0; $i < count($code); $i++) { 
            $code->setIdUser(null);
        }

        $manager->remove($user);
        $manager->flush();

        return $this->redirectToRoute('admin');
    }
}

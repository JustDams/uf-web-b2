<?php

namespace App\Controller;

use App\Entity\Games;
use App\Entity\Users;
use App\Entity\Code;
use App\Form\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();

        $users = $this->getDoctrine()->getRepository(Users::class)->findAll();

        $games = $this->getDoctrine()
            ->getRepository(Games::class)
            ->search10LastGames();

        $formGames = $this->createFormBuilder()
            ->add('game', TextType::class, [
                'required' => true,
            ])
            ->add('submit', SubmitType::class)
            ->getForm();
        $formGames->handleRequest($request);

        if ($formGames->isSubmitted() && $formGames->isValid()) {
            $gameTitle=$formGames->get('game')->getData();
            $games = $this->getDoctrine()
                ->getRepository(Games::class)
                ->findGamesByString($gameTitle);
        }

        $formUsers = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('submit', SubmitType::class)
            ->getForm();
        $formUsers->handleRequest($request);

        if ($formUsers->isSubmitted() && $formUsers->isValid()) {
            $userName=$formUsers->get('name')->getData();
            $users = $this->getDoctrine()
                ->getRepository(Users::class)
                ->findUsersByString($userName);
        }

        $allGames = $this->getDoctrine()->getRepository(Games::class)->findAll();
        $stock = 0;

        for ($i=0; $i < count($allGames); $i++) {
            $stock += $allGames[$i]->getStock();
        }

        $allPurchases = $this->getDoctrine()->getRepository(Code::class)->findAll();
        $lastWeekPurchases = $this->getDoctrine()->getRepository(Code::class)->LastWeekPurchases();
        $purchase = 0;
        $purchase7 = 0;

        for ($i=0; $i < count($allPurchases); $i++) {
            $purchase += $allPurchases[$i]->getPrice();
        }
        for ($i=0; $i < count($lastWeekPurchases); $i++) {
            $purchase7 += $lastWeekPurchases[$i]->getPrice();
        }

        $allOrders = $this->getDoctrine()->getRepository(Code::class)->findAll();
        $LastWeekOrders = $this->getDoctrine()->getRepository(Code::class)->LastWeekOrders();
        $order = count($allOrders);
        $order7 = count($LastWeekOrders);

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

        if ($user != null) {
            $role = $user->getRoles();
            if ($role[0] == 'ROLE_ADMIN') {
                return $this->render('admin/admin.html.twig', [
                    'purchase7' => $purchase7,
                    'order7' => $order7,
                    'stock' => $stock,
                    'purchase' => $purchase,
                    'order' => $order,
                    'games' => $games,
                    'user' => $user,
                    'role' => $role,
                    'users' => $users,
                    'searchform' => $searchForm->createView(),
                    'formGames' => $formGames->createView(),
                    'formUsers' => $formUsers->createView()
                ]);
            } else {
                return $this->redirectToRoute('index');
            }
        } else {
            return $this->redirectToRoute('index');
        }
    }


}

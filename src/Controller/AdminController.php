<?php

namespace App\Controller;

use App\Entity\Games;
use App\Entity\Users;
use App\Entity\Code;
use App\Form\SearchFormType;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    public function findOrders($page)
    {
        $first = 1;
        $second = 10;

        if ($page > 1) {
            $first += (10 * ($page - 1));
            $second += (10 * ($page - 1));
        }

        $games = $this->getDoctrine()->getRepository(Code::class)->findAll();
        $showOrders = [];

        if ( count($games) >= $second) {
            for ($i = $first; $i <= $second; $i++) {
                $showOrders[$i] = $games[$i];
            }
        } else {
            $this->addFlash('errors','There is nothing on this page.');
        }

        return $showOrders;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function admin(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();

        $allUsers = $this->getDoctrine()->getRepository(Users::class)->findAll();

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
            $gameTitle = $formGames->get('game')->getData();
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
            $userName = $formUsers->get('name')->getData();
            $users = $this->getDoctrine()
                ->getRepository(Users::class)
                ->findUsersByString($userName);
        }

        $allGames = $this->getDoctrine()->getRepository(Games::class)->findAll();
        $stock = 0;

        for ($i = 0; $i < count($allGames); $i++) {
            $stock += $allGames[$i]->getStock();
        }

        $allPurchases = $this->getDoctrine()->getRepository(Code::class)->findAll();
        $LastWeekOrders = $this->getDoctrine()->getRepository(Code::class)->LastWeekOrders();
        $purchase = 0;
        $purchase7 = $LastWeekOrders;

        for ($i = 0; $i < count($allPurchases); $i++) {
            $purchase += $allPurchases[$i]->getPrice();
        }

        $allOrders = $this->getDoctrine()->getRepository(Code::class)->findAll();

        $order = count($allOrders);
        $order7 = count($LastWeekOrders);

        $searchForm = $this->createForm(SearchFormType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        $code = $this->getDoctrine()->getRepository(Code::class)->search10LastOrder();

        $formCode = $this->createFormBuilder()
            ->add('page', TextType::class)
            ->add('submit', SubmitType::class)
            ->getForm();
        $formCode->handleRequest($request);

        if ($formCode->isSubmitted() && $formCode->isValid()) {
            $page = $formCode->get('page')->getData();
            $code = $this->findOrders($page);
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
                    'allUsers' => $allUsers,
                    'code' => $code,
                    'formCode' => $formCode->createView(),
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

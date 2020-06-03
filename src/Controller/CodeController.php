<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Games;
use App\Form\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class CodeController extends AbstractController
{

    private function generateCode($length = 16, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function sendEmail(Request $request, MailerInterface $mailer, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $balance = $user -> getBalance();
        $game = $this->getDoctrine()->getRepository(Games::class)->find($id);
        $code = $this->generateCode();
        $gamePrice = $game -> getPrice();

        if ($balance < $gamePrice) {
            $email = (new Email())
                ->from('noreply@de-weerd.name')
                ->to($user->getEmail())
                ->subject('Not enough Money')
                ->text('You don\'t have enough money to buy' . $game->getTitle().'. Your balance is at' . $balance);
        }
        else {
            $email = (new Email())
                ->from('noreply@de-weerd.name')
                ->to($user->getEmail())
                ->subject('Activation code of ' . $game->getTitle())
                ->text('Your activation code for the game ' . $game->getTitle() . ' is ' . $code);
            $user->setBalance($balance - $gamePrice);
            $entityManager->persist($user);
            $entityManager->flush();
        }
        $mailer->send($email);

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        return $this->render('code/index.html.twig', [
            'searchform' => $form->createView(),
            'code' => $code,
        ]);
    }

    /**
     * @Route("/addToCart/{id}", name="addToCart")
     */
    public function addToCart($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $game = $this->getDoctrine()->getRepository(Games::class)->find($id);
        $item = new Cart();

        $item->setIdUser($user);
        $item->setIdGame($game);

        $entityManager->persist($item);
        $entityManager->flush();

        return $this->redirectToRoute('game', [
            'id' => $id
        ]);
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function cart(Request $request)
    {
        $user = $this->getUser();

        if ($user == null) {
            return $this->redirectToRoute('index');
        }

        $cartId = $this->getDoctrine()->getRepository(Cart::class)->findBy([
            'idUser' => $user->getId(),
        ]);
        $games = [];

        for ($i=0; $i < count($cartId); $i++) { 
            $games[$i] = $this->getDoctrine()->getRepository(Games::class)->find($cartId[$i]->getIdGame());
        }

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        return $this->render('/code/cart.html.twig', [
            'user' => $user,
            'cartId' => $cartId,
            'games' => $games,
            'searchform' => $form->createView(),
        ]);
    }

     /**
     * @Route("/removeFromCart/{id}", name="removeFromCart")
     */
    public function removeFromCart($id)
    {
        $user = $this->getUser();
        $manager = $this->getDoctrine()->getManager();
        $item = $manager->find(Cart::class,$id);
        if($item != null) {
            $manager->remove($item);
            $manager->flush();    
        }   

        return $this->redirectToRoute('cart');
    }
}

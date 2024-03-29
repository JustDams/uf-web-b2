<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Code;
use App\Entity\Games;
use App\Form\SearchFormType;
use DateTime;
use Dompdf\Dompdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;
use Twig\Environment;

class CodeController extends AbstractController
{

    private $twig;
    private $pdf;
    public function __construct(Environment $twig, Pdf $pdf)
    {
        $this->twig = $twig;
        $this->pdf = $pdf;
    }

    private function generateCode($length = 16, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $code = $this->getDoctrine()->getRepository(Code::class)->findBy([
            'code' => $randomString
        ]);

        if (count($code) > 0) {
            $this->generateCode();
        } else {
            return $randomString;
        }
    }

    public function sendEmail(Request $request, MailerInterface $mailer,$allGames, $priceGames, $countGames, $activationCodes)
    {
        $user = $this->getUser();
        $str = "";
        $html = $this->twig->render("code/pdftemplate.html.twig", [
            'user' => $user,
            'priceGames' => $priceGames,
            'countGames' => $countGames
        ]);

        $pdf = $this->pdf->getOutputFromHtml($html);

        for ($i = 0; $i < count($allGames); $i++) {
            $str .= 'Your activation code for the game ' .
                $allGames[$i]->getTitle() . ' (console : '.$allGames[$i]->getConsole() .') is ' . $activationCodes[$i]->getCode() . '<br />';
        }

        $email = (new Email())
            ->from('noreply@de-weerd.name')
            ->to($user->getEmail())
            ->subject('Activation code of your games') //. $game->getTitle())
            ->html($str)
            ->attach($pdf, sprintf('document.pdf'));
        $mailer->send($email);
    }

    /**
     * @Route("/addToCart/{id}", name="addToCart")
     */
    public function addToCart($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($user != null) {
            $game = $this->getDoctrine()->getRepository(Games::class)->find($id);
            $stock = $game->getStock();
            if ($stock < 1) {
                $this->addFlash('errors', 'The stock is empty for this game, come back later.');
                return $this->redirectToRoute('game', [
                    'id' => $id
                ]);
            }
            $item = new Cart();

            $game->setStock($stock - 1);
            $item->setIdUser($user);
            $item->setIdGame($game);

            $entityManager->persist($item);
            $entityManager->persist($game);
            $entityManager->flush();
        } else {
            $this->addFlash('errors', 'You have to be connected to do that.');
        }

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
        $manager  = $this->getDoctrine()->getManager();

        if ($user == null) {
            return $this->redirectToRoute('index');
        }

        $cartId = $this->getDoctrine()->getRepository(Cart::class)->findBy([
            'idUser' => $user->getId(),
        ]);
        $games = [];
        $totalPrice = 0;

        for ($i = 0; $i < count($cartId); $i++) {
            $games[$i] = $this->getDoctrine()->getRepository(Games::class)->find($cartId[$i]->getIdGame());
            $totalPrice += $games[$i]->getPrice();
        }

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        $balance = $user->getBalance();
        $formMoney = $this->createFormBuilder()
            ->add('balance', MoneyType::class, [
                'required' => true,
                'currency' => 'USD'
            ])
            ->add('submit', SubmitType::class)
            ->getForm();
        $formMoney->handleRequest($request);

        if ($formMoney->isSubmitted() && $formMoney->isValid()) {
            $addBalance = $formMoney->getData()['balance'];
            if ($addBalance <= 0) {
                $this->addFlash('errors', 'This value is not valid.');
                return $this->redirectToRoute('cart');
            }
            $user->setBalance($balance + $addBalance);
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('cart');
        }

        return $this->render('/code/cart.html.twig', [
            'user' => $user,
            'cartId' => $cartId,
            'games' => $games,
            'totalPrice' => $totalPrice,
            'formMoney' => $formMoney->createView(),
            'searchform' => $form->createView(),
        ]);
    }

    /**
     * @Route("/removeFromCart/{id}", name="removeFromCart")
     */
    public function removeFromCart($id)
    {
        $user = $this->getUser();
        if ($user != null) {
            $manager = $this->getDoctrine()->getManager();
            $item = $manager->find(Cart::class, $id);
            if ($item != null) {
                $game = $item->getIdGame();
                $stock = $game->getStock();
                $game->setStock($stock + 1);

                $manager->persist($game);
                $manager->remove($item);
                $manager->flush();
            }
        }
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/buy", name="buy")
     */
    public function buy(Request $request, MailerInterface $mailer)
    {
        $user = $this->getUser();
        $manager = $this->getDoctrine()->getManager();
        if ($user == null) {
            return $this->redirectToRoute('index');
        }

        $cart = $this->getDoctrine()->getRepository(Cart::class)->findBy([
            'idUser' => $user->getId(),
        ]);
        
        $gamesId = [];
        $priceGames = [];
        $countGames = [];
        $allGames = [];
        
        for ($i = 0; $i < count($cart); $i++) {
            $gamesId[$i] = $cart[$i]->getIdGame()->getId();
            $countGames[$i] = $cart[$i]->getIdGame()->getTitle();
            $allGames[$i] = $cart[$i]->getIdGame();
        }

        $gamesId = array_count_values($gamesId);
        $countGames = array_count_values($countGames);
        $totalPrice = 0;

        foreach ($gamesId as $id => $amount) {
            $game = $this->getDoctrine()->getRepository(Games::class)->find($id);
            $totalPrice += $game->getPrice() * $amount;
            array_push($priceGames, $game->getPrice());
        }

        $balance = $user->getBalance();
        $activationCodes = [];

        if (($balance - $totalPrice) >= 0) {
            $user->setBalance($balance - $totalPrice);
            $carts = $user->getCarts();
            for ($i = 0; $i < count($carts); $i++) {
                $activationCode = $this->generateCode();
                $game = $carts[$i]->getIdGame();

                $code = new Code();
                $code->setIdUser($user);
                $code->setIdGame($game);
                $code->setCode($activationCode);
                $code->setUsed(0);
                $code->setPrice($game->getPrice());
                $code->setPurchaseDate(new DateTime('now'));

                $activationCodes[$i] = $code;

                $manager->persist($code);
                $manager->remove($carts[$i]);
            }
            $manager->flush();
            $this->sendEmail($request, $mailer,$allGames, $priceGames, $countGames ,$activationCodes);
 
            $manager->flush();

            $this->addFlash('success', 'You\'ll receive the activation(s) code(s) on your email.');
            return $this->redirectToRoute('cart');
        } else {
            $this->addFlash('errors', 'You don\'t have enough money.');
            return $this->redirectToRoute('cart');
        }
    }
}

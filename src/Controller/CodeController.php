<?php

namespace App\Controller;

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

    /**
     * @Route("/code/{id}", name="code")
     */
    public function sendEmail(Request $request, MailerInterface $mailer, $id)
    {
        $user = $this->getUser();
        $game = $this->getDoctrine()->getRepository(Games::class)->find($id);
        $code = $this->generateCode();

        $email = (new Email())
            ->from('noreply@de-weerd.name')
            ->to($user->getEmail())
            ->subject('Activation code of ' . $game->getTitle())
            ->text('Your activation code for the game ' . $game->getTitle() . ' is ' . $code);

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
}

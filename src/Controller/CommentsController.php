<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Form\CommentsFormType;
use App\Form\SearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends AbstractController
{

    /**
     * @Route("/editComment/{id}", name="editComment")
     */
    public function editComment(Request $request, $id)
    {
        $user = $this->getUser();
        $comment = $this->getDoctrine()->getRepository(Comments::class)->find($id);

        if ($user == null) {
            return $this->redirectToRoute('game', [
                'id' => $comment->getIdGame()->getId()
            ]);
        } else if ($comment->getIdUser() != $user && $user->getRoles()[0] != 'ROLE_ADMIN') {
            return $this->redirectToRoute('game', [
                'id' => $comment->getIdGame()->getId()
            ]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $commentForm = $this->createForm(CommentsFormType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('game', [
                'id' => $comment->getIdGame()->getId()
            ]);
        }

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData()->getTitle();
            return $this->redirectToRoute('search', ['game' => $data]);
        }

        return $this->render('comments/editComment.html.twig', [
            'comment' => $comment,
            'searchform' => $form->createView(),
            'commentForm' => $commentForm->createView(),
            'user' => $user,
        ]);
    }

     /**
     * @Route("/removeComment/{id}", name="removeComment")
     */
    public function removeComment($id) {
        $user = $this->getUser();
        $comment = $this->getDoctrine()->getRepository(Comments::class)->find($id);

        if($comment == null) {
            return $this->redirectToRoute('index');
        } else if ($comment->getIdUser() != $user and $user->getRoles()[0] != "ROLE_ADMIN") {
            return $this->redirectToRoute('index');
        }

        $manager = $this->getDoctrine()->getManager();

        $manager->remove($comment);
        $manager->flush();

        return $this->redirectToRoute('game', [
            'id' => $comment->getIdGame()->getId()
        ]);
    }
}

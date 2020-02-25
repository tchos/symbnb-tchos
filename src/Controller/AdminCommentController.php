<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminCommentController extends AbstractController
{
    /**
     * Cette fonction récupère la liste des commentaires
     * 
     * @Route("/admin/comments", name="admin_comment_index")
     * 
     * @return Response
     */
    public function index(CommentRepository $repo)
    {
        // On peut aussi utiliser le repository de doctrine pour récupérer les commentaires
        //$repo = $this->getDoctrine()->getRepository(Comment::class);

        // Récupération de la liste des commentaires
        $comments = $repo->findAll();
        
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $comments
        ]);
    }

    /**
     * Permet à l"administrateur de modifier un commentaire
     * 
     * @Route("admin/comments/{id}/edit", name="admin_comment_edit")
     *
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Comment $comment, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', 
                "Le commentaire n° <strong>{$comment->getId()}</strong> a bien été modifié !");
        }

        return $this->render('admin/comment/edit.html.twig',[
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet à l'administrateur de supprimer un commentaire sur une annonce
     * 
     * @Route("admin/comments/{id}/delete", name="admin_comment_delete")
     *
     * @param Comment $comment
     * @return Response
     */
    public function delete(Comment $comment, EntityManagerInterface $manager)
    {
        $manager->remove($comment);
        $manager->flush();

        // fenetre d'alert de suppression avec succès
        $this->addFlash('success', 
            "Le commentaire de <strong>{$comment->getAuthor()->getFullName()}</strong> sur l'annonce
            <strong>{$comment->getAd()->getTitle()}</strong> a bien été supprimée.");

        // Redirection sur la page affichant les commentaires
        return $this->redirectToRoute('admin_comment_index');
    }
}

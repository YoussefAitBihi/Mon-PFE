<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentAdminType;
use App\Service\PaginationService;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page}", name="admin_comment_index", requirements={"page"="\d+"})
     */
    public function index(PaginationService $pagination, $page = 1) {

        $pagination->setEntityClassName(Comment::class)
                   ->setCurrentPage($page);

        return $this->render('admin/admin_comment/comments.html.twig', [
            'comments'      => $pagination->getData(),
            'pages'         => $pagination->allPages(),
            'currentPage'   => $page
        ]);
    }

    /**
     * Permet d'afficher et modifier un commentaire
     * 
     * @Route("/admin/comments/{id}/edit", name="admin_comment_edit")
     *
     * @param Comment $comment
     * @return Response
     */
    public function edit(Comment $comment, Request $request, ObjectManager $manager) {

        $form = $this->createForm(CommentAdminType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()):
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Ce commentaire {$comment->getComment()} a été modifié avec succès"
            );

            return $this->redirectToRoute("admin_comment_index");
        endif;

        return $this->render('admin/admin_comment/edit.html.twig', [
            'comment'   => $comment,
            'form'      => $form->createView() 
        ]);
    }
    
    /**
     * Permet de supprimer un commentaire
     * 
     * @Route("/admin/comments/{id}/remove", name="admin_comment_remove")
     *
     * @param Comment $comment
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Comment $comment, ObjectManager $manager) {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'success',
            "Ce commentaire {$comment->getId()} a bien été supprimé"
        );

        return $this->redirectToRoute('admin_comment_index');
    }
}

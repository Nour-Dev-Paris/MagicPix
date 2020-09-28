<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;
use App\Form\AccountType;
use App\Form\AdminCommentType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminGaleryController extends AbstractController
{
    /**
     * @Route("/admin/galerys", name="admin_galerys_index")
     */
    public function index(UserRepository $repo)
    {
        return $this->render('admin/galery/index.html.twig', [
            'users' => $repo->findAll()
        ]);
    }

    /**
     * Affiche le formulaire d'édition d'un utilisateur
     * 
     * @Route("/admin/users/{id}/edit", name="admin_users_edit")
     *
     * @param User $user
     * @return Response
     */
    public function editUser(User $user, Request $request, EntityManagerInterface $manager) {
        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le compte de {$user->getPseudo()} a bien été modifié !"
            );
        }

        return $this->render('admin/galery/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un utilisateur
     * 
     * @Route("/admin/users/{id}/delete", name="admin_users_delete")
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function delete(User $user, EntityManagerInterface $manager) {
        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le compte de {$user->getPseudo()} a bien été supprimé !"
        );

        return $this->redirectToRoute('admin_galerys_index');
    }

    /**
     * Permet d'éditer un commentaire
     * 
     * @Route("/admin/comments/{id}/edit", name="admin_comment_edit")
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function editComment(Comment $comment, Request $request, EntityManagerInterface $manager) {

        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire de {$comment->getAuthor()->getPseudo()} a bien été modifié !"
            );
        }
        
        return $this->render('admin/galery/edit_comment.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un commentaire
     * 
     * @Route("/admin/comments/{id}/delete", name="admin_comment_delete")
     * 
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function deleteComment(Comment $comment, EntityManagerInterface $manager) {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le commentaire de {$comment->getAuthor()->getPseudo()} a bien été supprimé !"
        );

        return $this->redirectToRoute('admin_galerys_index');
    }
}

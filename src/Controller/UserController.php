<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/users/{page<\d+>?1}", name="users_index")
     */
    public function index(UserRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(User::class)
                   ->setPage($page)
                   ->setLimit(4);

        return $this->render('user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }


    /**
     * Affiche une galerie d'un photographe
     * 
     * @Route("/users/{slug}/{page<\d+>?1}", name="users_show")
     * 
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function show($slug, UserRepository $repo, Request $request, EntityManagerInterface $manager, $page, PaginationService $pagination) {
        $comment = new Comment();
        $user = $repo->findOneBySlug($slug);

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser())
                    ->setGalery($user);
                    

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre commentaire a bien été pris en compte !"
            );
        }

        $pagination->setEntityClass(Comment::class)
                   ->setPage($page)
                   ->setLimit(5);

        return $this->render('user/show.html.twig' , [
            'user' => $user,
            'pagination' => $pagination,
            'form' => $form->createView()
        ]); 
    }

}

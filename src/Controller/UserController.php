<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\DateTime;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="users_index")
     */
    public function index(UserRepository $repo)
    {
        $users = $repo->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }


    /**
     * Affiche une galerie d'un photographe
     * 
     * @Route("/users/{slug}", name="users_show")
     * 
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function show($slug, UserRepository $repo, Request $request, EntityManagerInterface $manager) {
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

        $user = $repo->findOneBySlug($slug);

        return $this->render('user/show.html.twig' , [
            'user' => $user,
            'form' => $form->createView()
        ]); 
    }

}

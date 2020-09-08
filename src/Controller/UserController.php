<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * @return Response
     */
    public function show($slug, UserRepository $repo) {
        $user = $repo->findOneBySlug($slug);

        return $this->render('user/show.html.twig' , [
            'user' => $user
        ]); 
    }

}

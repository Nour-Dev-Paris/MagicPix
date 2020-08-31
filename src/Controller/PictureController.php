<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PictureController extends AbstractController
{
    /**
     * @Route("/picture", name="picture")
     */
    public function index()
    {
        return $this->render('picture/index.html.twig', [
            'controller_name' => 'PictureController',
        ]);
    }

    /**
     * Amène à la page d'accueil
     * 
     * @Route("/", name="home")
     * 
     * @return void
     */
    public function home()
    {
        return $this->render('picture/home.html.twig');
    }
}

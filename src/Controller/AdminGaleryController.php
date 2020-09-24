<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
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
    public function editUser(User $user) {
        $form = $this->createForm(RegistrationType::class, $user);

        return $this->render('admin/galery/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}

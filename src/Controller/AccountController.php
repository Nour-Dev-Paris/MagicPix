<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Image;
use App\Form\ImageType;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use App\Repository\UserRepository;
use App\Repository\ImageRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer le formulaire de connexion
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        $user = $this->getUser();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' =>$username
        ]);

        return $this->redirectToRoute('users_show', [
            'slug' => app.user.slug
        ]);
    }

    /**
     * Permet de se déconnecter
     * 
     * @Route("/logout", name="account_logout")
     *
     * @return void
     */
    public function logout() {}

    /**
     * 
     * Permet d'afficher le formulaire d'inscription
     * 
     * @Route("/register", name="account_register")
     *
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder ) {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été crée ! Vous pouvez maintenant vous connecter !'
            );

            return $this->redirectToRoute('account_login');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     * 
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager) {
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) { 
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Modification du profil effectuée"
            );
        }

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     * 
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {
        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // 1. Vérifiez que le oldPassword du formulaire soit le même que le password de l'User
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                //Gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié !"
                );

                return $this->redirectToRoute('home');
            }
    
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'ajouter des images via le formulaire
     * 
     * @Route("/account/upload", name="account_upload")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function uploadPicture(Request $request,EntityManagerInterface $manager) {
        $image = new Image();
        $user = new User();
        
        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach($user->getImages() as $image) {
                $image->setUser($user);
                $manager->persist($image);
            }

            $image->setUser($this->getUser());

            $manager->persist($image);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre photo a bien été ajoutée ! Vous pouvez ajoutez des photos supplémentaires."
            );
        }

        return $this->render('account/upload.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet l'affichage de suppression de photos
     * 
     * @Route("/account/{slug}/show_delete", name="show_delete")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function showDelete($slug, UserRepository $repo, EntityManagerInterface $manager)
    {
        $user = $repo->findOneBySlug($slug);

        return $this->render('account/delete_picture.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Permet la suppression de photos
     * 
     * @Route("/account/picture_delete/{id}", name="picture_delete")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function deletePicture(Image $image, EntityManagerInterface $manager, Request $request)
    {

        $manager->remove($image);

        $manager->flush();

        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}

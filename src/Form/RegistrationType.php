<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'pseudo', 
                TextType::class, 
                $this->getConfiguration("Pseudo", "Votre pseudo...")
            )
            ->add(
                'email', 
                EmailType::class, 
                $this->getConfiguration("Email", "Votre email...")
            )
            ->add(
                'avatar',
                UrlType::class, 
                $this->getConfiguration("Photo de profil", "Url de votre avatar...", [ 'required' => false, 'empty_data' => 'https://zupimages.net/up/20/37/q6qp.jpg' ]), 
            )
            ->add(
                'hash', 
                PasswordType::class, 
                $this->getConfiguration("Mot de passe", "Choisissez un bon mot de passe...")
            )
            ->add(
                'passwordConfirm',
                PasswordType::class,
                $this->getConfiguration("Confirmation de mot de passe", "Veuillez confirmer votre mot de passe")
            )
            ->add(
                'introduction', 
                TextType::class, 
                $this->getConfiguration("Introduction", "Présentez vous en quelques mots...") 
            )
            ->add(
                'description', 
                TextareaType::class, 
                $this->getConfiguration("Description détaillé", "C'est le moment de présentez votre galerie...")
            )
            ->add(
                'coverImage',
                UrlType::class, 
                $this->getConfiguration("Photo de couverture", "Url de la photo de couverture de votre galerie...", [ 'required' => false, 'empty_data' => 'http://lorempixel.com/output/abstract-q-g-730-480-8.jpg' ]), 
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

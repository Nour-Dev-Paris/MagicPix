<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ImageType extends AbstractType
{
    /**
     * Permet d'avoir la configuration de base d'un champ
     *
     * @param string $label
     * @param string $placeholder
     * @return array
     */
    private function getConfiguration($label, $placeholder) {
        return [
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ],
        ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('url', UrlType::class, $this->getConfiguration("Url de l'image", "Veuillez entrez l'URL de votre image"))
            ->add('caption', TextType::class, $this->getConfiguration("Légende de l'image", "Veuillez ajoutez la légende de votre image"))
            ->add('imageFile', VichImageType::class, $this->getConfiguration("Téléchargez votre image", "Veuillez choisir un fichier"))
            // ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}

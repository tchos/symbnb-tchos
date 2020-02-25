<?php

namespace App\Form;

use App\Entity\Comment;
use App\form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rating', IntegerType::class, $this->getConfiguration("Note sur 5", 
            "Veuillez indiquer une note entre 0 et 5", [
                    'attr' => [
                        'min' => 0,
                        'max' => 5
                    ]
                ]))
            ->add('content', TextareaType::class, $this->getConfiguration("Votre avis / témoignage",
                "N'hésitez pas à être précis, cela aidera nos futurs voyageurs !"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}

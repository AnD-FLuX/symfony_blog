<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('title', TextType::class, [
                'label' => 'Enter title',
                'attr'=>[
                'class'=>'form-control',
              ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Enter content',
                'attr'=>[
                'class'=>'form-control mb-3'
              ]
            ])
            ->add('image', FileType::class, [
               'label' => 'Some image',
               'required' => false,
               'mapped' => false
           ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}

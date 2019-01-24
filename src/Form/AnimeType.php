<?php

namespace App\Form;

use App\Entity\Animes;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false
            ])
            ->add('image', TextType::class, [
                'label' => false
            ])
            ->add('imageCard', TextType::class, [
                'label' => false
            ])
            ->add('imageCardBlur', TextType::class, [
                'label' => false
            ])
            ->add('synopsis', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'rows' => 5
                ]
            ])
            ->add('duration', IntegerType::class, [
                'label' => false
            ])
            ->add('type', EntityType::class, [
                'label' => false,
                'class' => Type::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
                'label_attr' => ['class' => 'checkbox-custom']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Animes::class,
        ]);
    }
}

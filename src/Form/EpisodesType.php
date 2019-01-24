<?php

namespace App\Form;

use App\Entity\Episodes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EpisodesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('season', IntegerType::class, [
                'label' => 'Entrez une saison'
            ])
            ->add('episodes', FileType::class, [
                'mapped' => false,
                'label' => 'Choisissez un épisode'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Episodes::class,
        ]);
    }
}

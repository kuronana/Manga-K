<?php

namespace App\Service;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Services extends AbstractController
{
    public function Registration(Request $request,
                                 UserPasswordEncoderInterface $encoder)
    {

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword( $user, $user->getPassword());
            $user->setPassword($hash);
            $user->setRoles('ROLE_USER');

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'registration_valid',
                'Inscription validée =)'
            );
        } elseif ($form->isSubmitted()) {
            $this->addFlash(
                'registration_invalid',
                'Information d\'inscription incorrectes.'
            );
        }
        return $form;
    }
}
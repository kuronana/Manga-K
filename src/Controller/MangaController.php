<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MangaController extends AbstractController
{
    /**
     * @Route("/manga", name="manga")
     */
    public function index(Request $request,
                          UserPasswordEncoderInterface $encoder,
                          AuthenticationUtils $utils)
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

        return $this->render('manga/index.html.twig', [
            'form' => $form->createView(),
            'last_username' => $utils->getLastUsername(),
            'error' => $utils->getLastAuthenticationError()
        ]);
    }
}

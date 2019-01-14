<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package App\Controller
 * @Route("/")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="security_home")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function home(Request $request,
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
                'Informations d\'inscription incorrectes.'
            );
        }

        return $this->render('security/home.html.twig', [
            'form' => $form->createView(),
            'last_username' => $utils->getLastUsername(),
            'error' => $utils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/role", name="security_role")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function role()
    {
        $role = $this->getUser()->getRoles();
        if ($role == ['ROLE_USER']) {
            return $this->redirectToRoute('user_account');
        } elseif ($role == ['ROLE_ADMIN']) {
            return $this->redirectToRoute('admin_account');
        }
    }

    /**
     * @Route("/login", name="security_login")
     */
    public function login()
    {
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {

    }
}

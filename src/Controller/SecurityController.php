<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
     * @Route("/user/account", name="user_account")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("is_fully_authenticated()")
     */
    public function account(AuthenticationUtils $utils)
    {
        return $this->render('security/user/account.html.twig', [
            'last_username' => $utils->getLastUsername(),
            'error' => $utils->getLastAuthenticationError()
        ]);
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

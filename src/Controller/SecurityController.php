<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function home(AuthenticationUtils $utils)
    {
        if ($this->getUser()) {
            $role = $this->getUser()->getRoles();
            dump($role);

            return $this->render('security/home.html.twig', [
                'last_username' => $utils->getLastUsername(),
                'error' => $utils->getLastAuthenticationError(),
                'role' => $role
            ]);
        }

        return $this->render('security/home.html.twig', [
            'last_username' => $utils->getLastUsername(),
            'error' => $utils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/registration", name="registration",  condition="request.isXmlHttpRequest()")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function registration(Request $request,
                                 UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user, [
            'action' => $this->generateUrl($request->get('_route'))
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword( $user, $user->getPassword());
            $user->setPassword($hash);
            $user->setRoles('ROLE_USER');

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            return new Response('success');
        }
        return $this->render('security/_registration.html.twig', [
            'form' => $form->createView(),
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

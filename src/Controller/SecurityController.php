<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
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
                                 UserPasswordEncoderInterface $encoder,
                                 UserRepository $userRepository)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user, [
            'action' => $this->generateUrl($request->get('_route'))
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword( $user, $user->getPassword());
            $user->setPassword($hash);
            $user->setPicture('man.png');

            if ($userRepository->findAll()) {
                $user->setRoles('ROLE_USER');
            } else {
                $user->setRoles('ROLE_SUPER_ADMIN');
            }

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
     * @Route("/referer", name="referer_page")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function referer(AuthenticationUtils $utils)
    {
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        return $this->redirectToRoute('security_home', [
            'last_username' => $utils->getLastUsername(),
            'error' => $utils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {

    }
}

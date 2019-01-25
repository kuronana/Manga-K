<?php

namespace App\Controller;

use App\Form\AccountSettingAvatarType;
use App\Form\AccountSettingPasswordType;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    private $userRepository;
    private $filesystem;
    private $om;

    public function __construct(UserRepository $userRepository,
                                ObjectManager $om,
                                Filesystem $filesystem)
    {
        $this->userRepository = $userRepository;
        $this->filesystem = $filesystem;
        $this->om = $om;
    }


    /**
     * @Route("/account", name="user_account")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Security("is_granted('ROLE_USER')")
     */
    public function account(Request $request,
                            UserPasswordEncoderInterface $encoder)

    {
        $user = $this->getUser();
        $oldAvatar = $user->getPicture();


        ///////////////////////////////////////////////////////////////////////////////////////////
        //////////////////////////////   Formulaire MDP   /////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////////

        $formPassword = $this->createForm(AccountSettingPasswordType::class);
        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            if ($encoder->isPasswordValid($this->getUser(), $formPassword->get('old_password')->getData())) {
                $this->getUser()->setPassword($encoder->encodePassword($this->getUser(), $formPassword
                    ->get('password')->getData()));
                $this->om->flush();

                return new Response('new_password');
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////////////
        ///////////////////////////   Formulaire Avatar   /////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////////

        $formAvatar = $this->createForm(AccountSettingAvatarType::class);
        $formAvatar->handleRequest($request);

        if ($formAvatar->isSubmitted() && $formAvatar->isValid()) {
            $file = $formAvatar->get('file')->getData();


            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            ///////////////////////////////////////////////////////////////
            // Supression de l'ancienne image après ajout de la nouvelle //
            ///////////////////////////////////////////////////////////////

            if ($oldAvatar != "man.png") {

                $this->filesystem->remove($this->getParameter('images_directory') . '/' . $oldAvatar);
            }

            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
            }

            $user->setPicture($fileName);
            $this->om->flush();

            return new Response('change_avatar');
        }


        return $this->render('security/user/account.html.twig', [
            'formPassword' => $formPassword->createView(),
            'formAvatar' => $formAvatar->createView(),
            'user' => $user
        ]);
    }
}

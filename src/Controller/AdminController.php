<?php

namespace App\Controller;

use App\Entity\Animes;
use App\Entity\Type;
use App\Form\AccountSettingAvatarType;
use App\Form\AccountSettingPasswordType;
use App\Form\AnimeType;
use App\Repository\AnimesRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin")
 * Class AdminController
 * @package App\Controller
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AdminController extends AbstractController
{
    private $om;
    private $animesRepository;
    private $userRepository;

    public function __construct(ObjectManager $manager,
                                AnimesRepository $animesRepository,
                                UserRepository $userRepository)
    {
        $this->om = $manager;
        $this->animesRepository = $animesRepository;
        $this->userRepository = $userRepository;
    }

    //   !!!!!!!!!!!!!!!!!!!!!!!!!!   Partie Comptes   !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    /**
     * @Route("/account", name="my_admin_account")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function account(Request $request,
                            UserPasswordEncoderInterface $encoder)
    {
        $admin = $this->getUser();

        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!   Formulaire mdp   !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

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
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!   Formulaire Avatar   !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        $formAvatar = $this->createForm(AccountSettingAvatarType::class);
        $formAvatar->handleRequest($request);

        if ($formAvatar->isSubmitted() && $formAvatar->isValid()) {
            $file = $admin->getPicture();

            $fileName = md5(uniqid()).'.'.$file->guessExtension();


            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
            }

            $admin->setPicture($fileName);
            $this->om->flush();

            return new Response('new_avatar');
        }

        return $this->render('security/admin/account.html.twig', [
            'admin' => $admin,
            'formPassword' => $formPassword->createView(),
            'formAvatar' => $formAvatar->createView()
        ]);
    }

    /**
     * @Route("/account/list/user", name="user_list")
     * @return Response
     */
    public function  listAccountUser()
    {
        $users = $this->userRepository->findByRoles('ROLE_USER');

        return $this->render('/security/admin/userAccountList.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/account/list/admin", name="admin_list")
     * @return Response
     */
    public function  listAccountAdmin()
    {
        $admins = $this->userRepository->findByRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);

        return $this->render('/security/admin/adminAccountList.html.twig', [
            'admins' => $admins
        ]);
    }

    /**
     * @Route("/account/user/{id}", name="account_user")
     * @param $id
     * @return Response
     */
    public function AccountUser($id)
    {
        $user = $this->userRepository->findOneById($id);

        return $this->render('/security/admin/userAccount.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/account/admin/{id}", name="account_admin")
     * @param $id
     * @return Response
     */
    public function AccountAdmin($id)
    {
        $admin = $this->userRepository->findOneById($id);

        return $this->render('/security/admin/adminAccount.html.twig', [
            'admin' => $admin
        ]);
    }

    /**
     * @Route("/account/user/switchRole/{id}", name="switch_role")
     * @param $id
     * @return Response
     */
    public function AccountSwitchRole($id)
    {

        $user = $this->userRepository->findOneById($id);
        if ($user->getRoles() == ['ROLE_USER']) {
            $user->setRoles('ROLE_ADMIN');
        } else {
            $user->setRoles('ROLE_USER');
        }
        $this->om->persist($user);
        $this->om->flush();

        return new Response('switch');
    }

    /**
     * @Route("/account/user/switchRoleSup/{id}", name="switch_role_sup")
     * @param $id
     * @return Response
     */
    public function AccountSwitchRoleSup($id)
    {

        $user = $this->userRepository->findOneById($id);
        if ($user->getRoles() == ['ROLE_ADMIN']) {
            $user->setRoles('ROLE_SUPER_ADMIN');
            $this->om->persist($user);
            $this->om->flush();

            return new Response('switchSup');
        } else {
            $user->setRoles('ROLE_ADMIN');
            $this->om->persist($user);
            $this->om->flush();

            return new Response('switch');
        }
    }

    /**
     * @Route("/account/user/delete/{id}", name="user_delete")
     * @param $id
     * @return Response
     */
    public function deleteUser($id)
    {
        $user = $this->userRepository->findOneById($id);
        $this->om->remove($user);
        $this->om->flush();

        return new Response('success');
    }

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!   Partie Animés   !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    /**
     * @Route("/newAnime", name="new_anime")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAnime(Request $request)
    {
        $genres = $this->getDoctrine()->getRepository(Type::class)->findAll();

        $anime = new Animes();
        $formAnime = $this->createForm(AnimeType::class, $anime, [
            'action' => $this->generateUrl($request->get('_route'))
        ]);
        $formAnime->handleRequest($request);

        if ($formAnime->isSubmitted() && $formAnime->isValid()) {
            $this->om->persist($anime);
            $this->om->flush();

            return new Response('success');
        }
        return $this->render('security/admin/newAnime.html.twig', [
            'formAnime' => $formAnime->createView(),
            'genres' => $genres
        ]);
    }

    /**
     * @Route("/updateAnime/{id}", name="update_anime")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function updateAnime(Request $request,
                           $id)
    {
        $anime = $this->animesRepository->findOneBy([
            'id' => $id
        ]);
        $formUpdateAnime = $this->createForm(AnimeType::class, $anime);
        $formUpdateAnime->handleRequest($request);

        if ($formUpdateAnime->isSubmitted() && $formUpdateAnime->isValid()) {
            $this->om->persist($anime);
            $this->om->flush();

            return new Response('success');
        }

        return $this->render('security/admin/updateAnime.html.twig', [
            'formUpdateAnime' => $formUpdateAnime->createView(),
            'anime' => $anime
        ]);
    }

    /**
     * @Route("/deleteAnime/{id}", name="delete_anime")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function deleteAnime($id)
    {
        $anime = $this->animesRepository->findOneBy([
            'id' => $id
        ]);
            $this->om->remove($anime);
            $this->om->flush();

            return $this->redirectToRoute("animes");
    }
}

<?php

namespace App\Controller;

use App\Entity\Animes;
use App\Entity\Episodes;
use App\Entity\Type;
use App\Form\AccountSettingAvatarType;
use App\Form\AccountSettingPasswordType;
use App\Form\AnimeType;
use App\Form\EpisodesType;
use App\Repository\AnimesRepository;
use App\Repository\EpisodesRepository;
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
    private $episodeRepository;
    private $filesystem;

    public function __construct(ObjectManager $manager,
                                AnimesRepository $animesRepository,
                                UserRepository $userRepository,
                                EpisodesRepository $episodeRepository,
                                Filesystem $filesystem)
    {
        $this->om = $manager;
        $this->animesRepository = $animesRepository;
        $this->userRepository = $userRepository;
        $this->episodeRepository = $episodeRepository;
        $this->filesystem = $filesystem;
    }


    ///////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////   Partir Compte   /////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////

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
        $oldAvatar = $admin->getPicture();


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

            $admin->setPicture($fileName);
            $this->om->flush();

            return new Response('change_avatar');
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

     /////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////   Partie Animés   ////////////////////////////////////////////
   /////////////////////////////////////////////////////////////////////////////////////////////
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

            $this->filesystem->mkdir('assets/video/upload/anime/'.$anime->getName());

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

        /////////////////////////////////////////////////////////////////
        // Tableau contenant toutes les saisons et épisodes de l'animé //
        /////////////////////////////////////////////////////////////////

        $tab = [];
        $seasons = [];
        $issetSeasons = [];
        $emptySeasons = [];
        if (!$this->filesystem->exists('assets/video/upload/anime/'.$anime->getName())) {
            $this->filesystem->mkdir('assets/video/upload/anime/'.$anime->getName());
        }
        if (scandir('assets/video/upload/anime/'.$anime->getName())) {
            $dirs = scandir('assets/video/upload/anime/'.$anime->getName());
        }

        foreach ($dirs as $dir ) {
            if (!in_array($dir, ['.', '..'])) {
                $files = scandir('assets/video/upload/anime/'.$anime->getName().'/'.$dir);


                foreach ($files as $file ) {
                    if (!in_array($file, ['.', '..'])) {
                        $tab[$dir][] = $file;
                        $issetSeasons[] = $dir;
                    }
                }
                $seasons[] = $dir;
            }
        }

        /////////////////////////////////
        // recherche les saisons vides //
        /////////////////////////////////
        foreach ($seasons as $season ) {
            if (!in_array($season, $issetSeasons)) {
                $emptySeasons[] = $season;
            }
        }
        ///////////////////////////////////////////////////////////

        $formUpdateAnime = $this->createForm(AnimeType::class, $anime);
        $formUpdateAnime->handleRequest($request);

        if ($formUpdateAnime->isSubmitted() && $formUpdateAnime->isValid()) {
            $this->om->persist($anime);
            $this->om->flush();

            return new Response('success');
        }
        //////////////////////////////
        //  Rajouter des épisodes  ///
        //////////////////////////////

        $episode = new Episodes();

        $formEpisode = $this->createForm(EpisodesType::class, $episode);
        $formEpisode->handleRequest($request);

        if ($formEpisode->isSubmitted() && $formEpisode->isValid()) {
            $video = $formEpisode->get('episodes')->getData();

            $scanEpisodes = scandir('assets/video/upload/anime/' . $anime->getName() . '/Saison ' . $episode->getSeason());
            $episodes = [];

            foreach ($scanEpisodes as $eps) {
                if (!in_array( $eps, ['.', '..'] )) {
                    $episodes[] = $eps;
                }
            }

            $count = count($episodes) + 1;
            if ($count < 10) {
                $fileName = 'Episode 0' . $count . '.mp4';
            } else {
                $fileName = 'Episode ' . $count . '.mp4';
            }


            try {
                $video->move(
                    $this->getParameter('anime_directory'). "/" . $anime->getName() . "/Saison " . $episode->getSeason(),
                    $fileName);
            } catch (FileException $e) {
            }

            $episode->setAnime($anime);
            $episode->setEpisodes($fileName);
            $this->om->persist($episode);
            $this->om->flush();

            return new Response('add_episode');
        }

        return $this->render('security/admin/updateAnime.html.twig', [
            'formUpdateAnime' => $formUpdateAnime->createView(),
            'formEpisode' => $formEpisode->createView(),
            'anime' => $anime,
            'tab' => $tab,
            'emptySeasons' => $emptySeasons,
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
        $this->filesystem->remove('assets/video/upload/anime/'.$anime->getName());
        $this->om->remove($anime);
        $this->om->flush();


        return new Response('delete');
    }

    /**
     * @Route("/addSeason/{id}", name="add_season")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addSeason($id)
    {
        $filesystem = new Filesystem();
        $anime = $this->animesRepository->findOneBy([
            'id' => $id
        ]);
        $dirs = scandir('assets/video/upload/anime/'.$anime->getName());
        $dir = [];
        foreach ($dirs as $dos ) {
            if (!in_array($dos, ['.', '..'])) {
                $dir[] = $dos;
            }
        }

        $count = count($dir)+1;
        $filesystem->mkdir('assets/video/upload/anime/'.$anime->getName().'/Saison '.$count);

        return $this->redirect($_SERVER['HTTP_REFERER']);

    }

    /**
     * @Route("/remSeason/{id}", name="rem_season")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remSeason($id)
    {
        $filesystem = new Filesystem();
        $anime = $this->animesRepository->findOneBy([
            'id' => $id
        ]);
        $dirs = scandir('assets/video/upload/anime/'.$anime->getName());
        $dir = [];
        foreach ($dirs as $dos ) {
            if (!in_array($dos, ['.', '..'])) {
                $dir[] = $dos;
            }
        }

        $filesystem->remove('assets/video/upload/anime/'.$anime->getName().'/Saison '.count($dir));

        return $this->redirect($_SERVER['HTTP_REFERER']);

    }
}

<?php

namespace App\Controller;

use App\Repository\AnimesRepository;
use App\Repository\EpisodesRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/animes")
 * Class AnimesController
 * @package App\Controller
 */
class AnimesController extends AbstractController
{
    private $utils;
    private $encoder;
    private $animesRepository;
    private $episodesRepository;
    private $om;
    public function __construct(AuthenticationUtils $utils,
                                UserPasswordEncoderInterface $encoder,
                                AnimesRepository $animesRepository,
                                EpisodesRepository $episodesRepository,
                                ObjectManager $om)
    {
        $this->utils = $utils;
        $this->encoder = $encoder;
        $this->animesRepository = $animesRepository;
        $this->episodesRepository = $episodesRepository;
        $this->om = $om;
    }

    /**
     * @Route("/", name="animes")
     */
    public function index()
    {
        $animes = $this->animesRepository->findAll();

        return $this->render('animes/index.html.twig', [
            'last_username' => $this->utils->getLastUsername(),
            'error' => $this->utils->getLastAuthenticationError(),
            'animes' => $animes
        ]);
    }

    /**
     * @Route("/stream/{name}/{EnCourSeason}/{EnCourEpisode}", name="stream")
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function streaming($name,
                              $EnCourSeason = 1,
                              $EnCourEpisode = 'Episode 1.mp4')
    {
        $anime = $this->animesRepository->findOneByName($name);

        $tab = [];
        $issetSeasons = [];

        if (scandir('assets/video/upload/anime/'.$name)) {
            $dirs = scandir('assets/video/upload/anime/'.$name);
        }

        foreach ($dirs as $dir ) {
            if (!in_array($dir, ['.', '..'])) {
                $files = scandir('assets/video/upload/anime/'.$name.'/'.$dir);


                foreach ($files as $file ) {
                    if (!in_array($file, ['.', '..'])) {
                        $tab[$dir][] = $file;
                        $issetSeasons[] = $dir;
                    }
                }
            }
        }

        return $this->render('animes/stream.html.twig', [
            'last_username' => $this->utils->getLastUsername(),
            'error' => $this->utils->getLastAuthenticationError(),
            'anime' => $anime,
            'EnCourSeason' => $EnCourSeason,
            'EnCourEpisode' => $EnCourEpisode,
            'tab' => $tab
        ]);
    }
}

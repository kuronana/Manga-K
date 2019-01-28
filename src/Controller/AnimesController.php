<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\AnimesRepository;
use App\Repository\CommentRepository;
use App\Repository\EpisodesRepository;
use Doctrine\Common\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    private $commentRepository;
    private $om;
    public function __construct(AuthenticationUtils $utils,
                                UserPasswordEncoderInterface $encoder,
                                AnimesRepository $animesRepository,
                                EpisodesRepository $episodesRepository,
                                CommentRepository $commentRepository,
                                ObjectManager $om)
    {
        $this->utils = $utils;
        $this->encoder = $encoder;
        $this->animesRepository = $animesRepository;
        $this->episodesRepository = $episodesRepository;
        $this->commentRepository = $commentRepository;
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
    public function streaming(Request $request,
                              $name,
                              $EnCourSeason = 1,
                              $EnCourEpisode = 'Episode 01.mp4')
    {
        $anime = $this->animesRepository->findOneByName($name);
        $currentComment = $this->commentRepository->findByAnime($anime);

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

        $comment = new  Comment();
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $comment->setAnime($anime);
            $comment->setUser($this->getUser());
            $comment->setDateTime(new \DateTime());
            $this->om->persist($comment);
            $this->om->flush();

            return new Response('success');
        }

        return $this->render('animes/stream.html.twig', [
            'last_username' => $this->utils->getLastUsername(),
            'error' => $this->utils->getLastAuthenticationError(),
            'formComment' => $formComment->createView(),
            'anime' => $anime,
            'currentComment' => $currentComment,
            'EnCourSeason' => $EnCourSeason,
            'EnCourEpisode' => $EnCourEpisode,
            'tab' => $tab
        ]);
    }
}

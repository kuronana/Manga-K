<?php

namespace App\Controller;

use App\Repository\AnimesRepository;
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
    private $om;
    public function __construct(AuthenticationUtils $utils,
                                UserPasswordEncoderInterface $encoder,
                                AnimesRepository $animesRepository,
                                ObjectManager $om)
    {
        $this->utils = $utils;
        $this->encoder = $encoder;
        $this->animesRepository = $animesRepository;
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
     * @Route("/stream/{name}", name="stream")
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function streaming($name)
    {
        $animes = $this->animesRepository->findAll();
        return $this->render('animes/stream.html.twig', [
            'last_username' => $this->utils->getLastUsername(),
            'error' => $this->utils->getLastAuthenticationError(),
            'animes' => $animes
        ]);
    }
}

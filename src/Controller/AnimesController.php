<?php

namespace App\Controller;

use App\Repository\AnimesRepository;
use App\Service\Services;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AnimesController extends AbstractController
{
    private $utils;
    private $services;
    private $encoder;
    private $animesRepository;
    public function __construct(AuthenticationUtils $utils,
                                Services $services,
                                UserPasswordEncoderInterface $encoder,
                                AnimesRepository $animesRepository)
    {
        $this->utils = $utils;
        $this->services = $services;
        $this->encoder = $encoder;
        $this->animesRepository = $animesRepository;
    }

    /**
     * @Route("/animes", name="animes")
     */
    public function index(Request $request)
    {
        $animes = $this->animesRepository->findAll();

        return $this->render('animes/index.html.twig', [
            'form' => $this->services->Registration($request, $this->encoder)->createView(),
            'last_username' => $this->utils->getLastUsername(),
            'error' => $this->utils->getLastAuthenticationError(),
            'animes' => $animes
        ]);
    }
}
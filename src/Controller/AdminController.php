<?php

namespace App\Controller;

use App\Entity\Animes;
use App\Entity\Type;
use App\Form\AnimeType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/admin")
 * Class AdminController
 * @package App\Controller
 */
class AdminController extends AbstractController
{
    private $om;

    public function __construct(ObjectManager $manager)
    {
        $this->om = $manager;
    }

    /**
     * @Route("/account", name="admin_account")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function account()
    {
        return $this->render('security/admin/account.html.twig');
    }

    /**
     * @Route("/list", name="list_anime")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAnime()
    {
        $animes = $this->getDoctrine()->getRepository(Animes::class)->findAll();

        return $this->render('security/admin/list_anime.html.twig', [
            'animes' => $animes
        ]);
    }

    /**
     * @Route("/newAnime", name="new_anime")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAnime(Request $request)
    {
        $genres = $this->getDoctrine()->getRepository(Type::class)->findAll();

        $anime = new Animes();
        $formAnime = $this->createForm(AnimeType::class, $anime);
        $formAnime->handleRequest($request);

        if ($formAnime->isSubmitted() && $formAnime->isValid()) {
            $this->om->persist($anime);
            $this->om->flush();

            $this->addFlash(
                'new_anime',
                'L\'animé '.$anime->getName().' a été ajouté avec succès.'
            );
        }
        return $this->render('security/admin/newAnime.html.twig', [
            'formAnime' => $formAnime->createView(),
            'genres' => $genres
        ]);
    }
}
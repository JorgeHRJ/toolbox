<?php

namespace App\Controller;

use App\Library\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="landing_")
 * @IsGranted("ROLE_USER")
 */
class LandingController extends BaseController
{
    /**
     * @Route("/", name="index")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('landing/index.html.twig', []);
    }
}

<?php

namespace App\Controller;

use App\Form\UserProfileType;
use App\Library\Controller\BaseController;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/perfil", name="profile_")
 * @IsGranted("ROLE_USER")
 */
class ProfileController extends BaseController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route(name="edit")
     *
     * @param Request $request
     * @return Response
     */
    public function profile(Request $request): Response
    {
        $user = $this->getUserInstance();

        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('app_error', $this->getFormErrorMessagesList($form, true));
                return $this->render('user/profile.html.twig', ['form' => $form->createView()]);
            }

            try {
                $this->userService->edit($user);

                $this->addFlash('app_success', '¡Perfil editado con éxito!');
            } catch (\Exception $e) {
                $this->addFlash('app_error', 'Hubo un problema a la hora de editar tu perfil');
            }
        }

        return $this->render('user/profile.html.twig', ['form' => $form->createView()]);
    }
}

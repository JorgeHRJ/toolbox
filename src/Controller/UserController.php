<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Library\Controller\BaseController;
use App\Library\Utils\Stringify;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/usuarios", name="user_")
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends BaseController
{
    private $userService;
    private $encoder;

    public function __construct(UserService $userService, UserPasswordEncoderInterface $encoder)
    {
        $this->userService = $userService;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/", name="index")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $this->userService->getAll()['data']
        ]);
    }

    /**
     * @Route("/crear", name="new")
     *
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('app_error', $this->getFormErrorMessagesList($form, true));
                return $this->render('user/new.html.twig', ['form' => $form->createView()]);
            }

            try {
                $password = $form->get('password')->getData() !== null
                    ? $form->get('password')->getData()
                    : Stringify::randomStr(12);
                $password = $this->encoder->encodePassword($user, $password);
                $user->setPassword($password);

                $this->userService->create($user);

                // TODO send mail

                $this->addFlash('app_success', '¡Usuario creado con éxito!');

                return $this->redirectToRoute('user_index');
            } catch (\Exception $e) {
                $this->addFlash('app_error', 'Hubo un problema a la hora de crear el usuario');
            }
        }

        return $this->render('user/new.html.twig', ['form' => $form->createView()]);
    }
}

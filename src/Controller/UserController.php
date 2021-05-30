<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Library\Controller\BaseController;
use App\Library\Event\NewUserNotificationEvent;
use App\Library\Utils\Stringify;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/usuarios", name="user_")
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends BaseController
{
    private UserService $userService;
    private EventDispatcherInterface $dispatcher;

    public function __construct(UserService $userService, EventDispatcherInterface $dispatcher)
    {
        $this->userService = $userService;
        $this->dispatcher = $dispatcher;
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
                $password = $form->get('password')->getData() ?? Stringify::randomStr(12);
                $user = $this->userService->new($user, $password);

                $event = new NewUserNotificationEvent($user->getEmail(), $password);
                $this->dispatcher->dispatch($event, NewUserNotificationEvent::EVENT);

                $this->addFlash('app_success', '¡Usuario creado con éxito!');

                return $this->redirectToRoute('user_index');
            } catch (\Exception $e) {
                $this->addFlash('app_error', 'Hubo un problema a la hora de crear el usuario');
            }
        }

        return $this->render('user/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/editar/{id}", name="edit", requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, int $id): Response
    {
        $user = $this->userService->get(null, $id);
        if (!$user instanceof User) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(UserRegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('app_error', $this->getFormErrorMessagesList($form, true));
                return $this->render('user/edit.html.twig', [
                    'form' => $form->createView(),
                    'user' => $user
                ]);
            }

            try {
                $this->userService->create($user);

                $this->addFlash('app_success', '¡Usuario editado con éxito!');

                return $this->redirectToRoute('user_index');
            } catch (\Exception $e) {
                $this->addFlash('app_error', 'Hubo un problema a la hora de editar el usuario');
            }
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}

<?php

namespace App\Controller\Api\v2;

use App\DTO\ManageUserDTO;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\Type\CreateUserType;
use App\Form\Type\UpdateUserType;
use App\Form\Type\UserType;
use App\Managers\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v2/user')]
class UserController extends AbstractController
{
    private const DEFAULT_PAGE = 0;
    private const DEFAULT_PER_PAGE = 20;

    public function __construct(
        private readonly UserManager $userManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly FormFactoryInterface $formFactory,
    ) {
    }

    #[Route(path: '', methods: ['POST'])]
    public function saveUserAction(Request $request): Response
    {
        $login = $request->request->get('login');
        $userId = $this->userManager->create($login);
        [$data, $code] = $userId === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'userId' => $userId], Response::HTTP_OK];

        return new JsonResponse($data, $code);
    }

    #[Route(path: '', methods: ['GET'])]
    public function getUsersAction(Request $request): Response
    {
        $perPage = $request->request->get('perPage');
        $page = $request->request->get('page');
        $users = $this->userManager->getUsers($page ?? self::DEFAULT_PAGE, $perPage ?? self::DEFAULT_PER_PAGE);
        $code = empty($users) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;

        return new JsonResponse(['users' => array_map(static fn(User $user) => $user->toArray(), $users)], $code);
    }

    #[Route(path: '/by-login/{user_login}', methods: ['GET'], priority: 2)]
    #[ParamConverter('user', options: ['mapping' => ['user_login' => 'login']])]
    public function getUserByLoginAction(User $user): Response
    {
        return new JsonResponse(['user' => $user->toArray()], Response::HTTP_OK);
    }

    #[Route(path: '/{user_id}', requirements: ['user_id' => '\d+'], methods: ['DELETE'])]
    #[Entity('user', expr: 'repository.find(user_id)')]
    public function deleteUserAction(User $user): Response
    {

        $result = $this->userManager->deleteUser($user);

        return new JsonResponse(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

    #[Route(path: '' , methods: ['PATCH'])]
    public function updateUserAction(Request $request): Response
    {
        $userId = $request->request->get('meetId');
        $login = $request->request->get('login');
        $result = $this->userManager->updateUserLogin($userId, $login);
        [$data, $code] = $result ? [null, Response::HTTP_NOT_FOUND] : [['user' => $result->toArray()], Response::HTTP_OK];

        return new JsonResponse($data, $code);
    }

    #[Route(path: '/create-user', name: 'create_user', methods: ['GET', 'POST'])]
    #[Route(path: '/update-user/{id}', name: 'update_user', methods: ['GET', 'PATCH'])]
    public function manageUserAction(Request $request, string $_route, ?int $id = null): Response
    {
        if ($id) {
            $user = $this->userManager->findUser($id);
            $dto = ManageUserDTO::fromEntity($user);
        }
        $form = $this->formFactory->create(
            $_route === 'create_user' ? CreateUserType::class : UpdateUserType::class,
            $dto ?? null,
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ManageUserDTO $userDto */
            $userDto = $form->getData();

            $this->userManager->saveUserFromDTO($user ?? new User(), $userDto);
        }

        return $this->renderForm('manageUser.html.twig', [
            'form' => $form,
            'isNew' => $_route === 'create_user',
            'user' => $user ?? null,
        ]);
    }
#[Route(path: '/test/{id}', methods: ['GET'])]
    public function test2($id): Response
    {
        dd($id, 'test2');
    }

    #[Route(path: '/test/{id}', requirements: ['id' => '\d+'] , methods: ['GET'], priority: 3)]
    public function test1($id): Response
    {
        dd($id, 'test1');
    }



}
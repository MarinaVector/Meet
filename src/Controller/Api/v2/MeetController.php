<?php

namespace App\Controller\Api\v2;

use App\Entity\Meet;
use App\Managers\MeetManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/v2/meet')]
class MeetController extends AbstractController
{
    private const DEFAULT_PAGE = 0;
    private const DEFAULT_PER_PAGE = 20;

    public function __construct(private readonly MeetManager $meetManager)
    {
    }
    #[Route(path: '', methods: ['POST'])]
    public function saveMeetAction(Request $request): Response
    {
        $text = $request->request->get('text');
        $format = $request->request->get('format');
        $authorId = $request->request->get('author_id');
        $meetId = $this->meetManager->create($text, $format);
        [$data, $code] = $meetId === null ?
            [['success' => false], Response::HTTP_BAD_REQUEST] :
            [['success' => true, 'meetId' => $meetId], Response::HTTP_OK];

        return new JsonResponse($data, $code);
    }


    #[Route(path: '', methods: ['GET'])]
    public function getMeetsAction(Request $request): Response
    {
        $perPage = $request->request->get('perPage');
        $page = $request->request->get('page');
        $meets = $this->meetManager->getMeets($page ?? self::DEFAULT_PAGE, $perPage ?? self::DEFAULT_PER_PAGE);
        $code = empty($meets) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;

        return new JsonResponse(['meets' => array_map(static fn(Meet $meet) => $meet->toArray(), $meets)], $code);
    }

    #[Route(path: '/{meet_id}', requirements: ['meet_id' => '\d+'], methods: ['DELETE'])]
    #[Entity('meet', expr: 'repository.find(meet_id)')]

    public function deleteMeetAction(Meet $meet): Response
    {

        $result = $this->meetManager->deleteMeet($meet);

        return new JsonResponse(['success' => $result], $result ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
    }

}

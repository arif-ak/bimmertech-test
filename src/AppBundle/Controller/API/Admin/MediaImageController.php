<?php

namespace AppBundle\Controller\API\Admin;

use AppBundle\Entity\MediaImage;
use AppBundle\Repository\MediaImageRepository;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MediaImageController
 * @package AppBundle\Controller\API\Admin
 */
class MediaImageController extends Controller
{
    /**
     * @var EntityRepository
     */
    private $mediaImageRepository;

    /**
     * MediaImageController constructor.
     * @param MediaImageRepository $mediaImageRepository
     */
    public function __construct(MediaImageRepository $mediaImageRepository)
    {
        $this->mediaImageRepository = $mediaImageRepository;
    }

    /**
     * Edit entity
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function edit(Request $request, $id)
    {
        /** @var MediaImage $mediaimage */
        if (!$mediaimage = $this->mediaImageRepository->find($id)) {
            return new JsonResponse('Data not found', JsonResponse::HTTP_NOT_FOUND);
        }

        if ($name = $request->request->get('name')) {
            $mediaimage->setName($name);
        }
        if ($alt = $request->request->get('alt')) {
            $mediaimage->setAlt($alt);
        };

        $this->mediaImageRepository->add($mediaimage);

        return new JsonResponse('Updated');
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function showImage(Request $request, $id): JsonResponse
    {
        if (!$mediaimage = $this->mediaImageRepository->find($id)) {
            return new JsonResponse('Data not found', JsonResponse::HTTP_NOT_FOUND);
        }
        return new JsonResponse($this->getData($mediaimage));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function showImages(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);
        $page = $request->get('page', 1);
        $sort = $request->get('sort', 'id');
        $order = $request->get('order', 'ASC');
        $mediaImages = $this->mediaImageRepository->getByFilter($limit, $page, $sort, $order);
        $array = [
            'page' => intval($page),
            'pages' => ceil(count($this->mediaImageRepository->findAll()) / $limit),
            'limit' => intval($limit)
        ];
        foreach ($mediaImages as $item) {
            /** @var MediaImage $item */
            $array['images'][] = $this->getData($item);
        }
        return new JsonResponse($array);
    }

    /**
     *  Delete image
     * @param $id
     * @return JsonResponse
     */
    public function deleteImage($id): JsonResponse
    {
        if (!$image = $this->mediaImageRepository->find($id)) {
            return new JsonResponse('Image not found', JsonResponse::HTTP_NOT_FOUND);
        }
        $this->mediaImageRepository->remove($image);
        return new JsonResponse(JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Get data
     *
     * @param $mediaImages
     * @return array
     */
    private function getData($mediaImages): array
    {
        /** @var MediaImage $mediaImages */
        return [
            'id' => $mediaImages->getId(),
            'name' => $mediaImages->getName(),
            'alt' => $mediaImages->getAlt(),
            'path' => $mediaImages->getPath(),
            'type' => $mediaImages->getType(),
            'createdAt' => $mediaImages->getCreatedAt() ? $mediaImages->getCreatedAt()->format('d-m-Y') : null,
        ];
    }

}
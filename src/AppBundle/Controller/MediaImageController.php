<?php

namespace AppBundle\Controller;

use AppBundle\Entity\MediaImage;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Uploader\ImageUploader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MediaImageController extends Controller
{
    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * MediaImageController constructor.
     * @param ImageUploader $imageUploader
     * @param ObjectManager $objectManager
     */
    public function __construct(ImageUploader $imageUploader, ObjectManager $objectManager)
    {
        $this->imageUploader = $imageUploader;
        $this->objectManager = $objectManager;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('@SyliusAdmin/MediaLibrary/Image/index.html.twig');
    }

    /**
     *  Upload image
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        /** @var UploadedFile $file */
        if (!$file = $request->files->get('image')) {
            return new JsonResponse('Bad parameter image', JsonResponse::HTTP_BAD_REQUEST);
        }
        $image = new MediaImage();
        $image->setName($request->get('name', 'no_name'));
        $image->setAlt($request->get('alt'));
        $image->setFile($file);
        $image->setType(substr(strrchr($file->getMimeType(), '/'), 1));
        $this->imageUploader->upload($image);
        $this->objectManager->persist($image);
        $this->objectManager->flush();
        $path = $image->getPath();
        $data = [
            'id' => $image->getId(),
            'path' => $path ?: null,
            'url' => $path ? $request->getSchemeAndHttpHost() . '/media/image/' . $image->getPath() : null,
            'name' => $image->getName(),
        ];
        return new JsonResponse($data, JsonResponse::HTTP_CREATED);
    }
}
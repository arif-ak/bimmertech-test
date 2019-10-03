<?php

namespace AppBundle\Controller\API\Admin;

use AppBundle\Entity\MediaFolder;
use AppBundle\Entity\MediaFolderInterface;
use AppBundle\Entity\MediaImage;
use AppBundle\Repository\MediaFolderRepository;
use AppBundle\Repository\MediaImageRepository;
use AppBundle\Serializer\Normalizer\MediaFolder\MediaFolderNormalizer;
use AppBundle\Serializer\Normalizer\MediaFolder\MediaFolderTreeNormalizer;
use AppBundle\Serializer\Normalizer\MediaFolder\MediaRootFolderNormalizer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class MediaFolderController
 * @package AppBundle\Controller\API\Admin
 */
class MediaFolderController extends Controller
{

    /**
     * @var MediaFolderRepository
     */
    private $mediaFolderRepository;

    /**
     * @var MediaImageRepository
     */
    private $mediaImagesRepository;

    /**
     * MediaFolderController constructor.
     * @param MediaFolderRepository $mediaFolderRepository
     * @param MediaImageRepository $mediaImageRepository
     */
    public function __construct(MediaFolderRepository $mediaFolderRepository, MediaImageRepository $mediaImageRepository)
    {
        $this->mediaFolderRepository = $mediaFolderRepository;
        $this->mediaImagesRepository = $mediaImageRepository;
    }

    /**
     * Get folder tree
     *
     * @return JsonResponse
     */
    public function tree()
    {
        $mediaFolders = $this->mediaFolderRepository->findBy(['parent' => null]);
        $data = [];
        if (!$mediaFolders) {
            return new JsonResponse('Folders not found', JsonResponse::HTTP_NOT_FOUND);
        }

        foreach ($mediaFolders as $item) {
            $data[] = (new MediaFolderTreeNormalizer())->normalize($item);
        }

        return new JsonResponse($data);
    }

    /**
     * Show folder
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function show(Request $request, $id)
    {
        /** @var MediaFolder $mediaFolder */
        if (!$mediaFolder = $this->mediaFolderRepository->find($id)) {
            return new JsonResponse('Folder not found', JsonResponse::HTTP_NOT_FOUND);
        }

        $data = array_merge(
            (new MediaFolderNormalizer())->normalize($mediaFolder, null),
            (new MediaRootFolderNormalizer())->normalizeImage($mediaFolder->getImages(), null, ['request' => $request]));


        $paginate = $this->get('app.helper.paginate_helper')->paginateArray($data);

        return new JsonResponse($paginate);
    }

    /**
     * Show root folder
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function showRoot(Request $request)
    {
        $mediaFolders = $this->mediaFolderRepository->findBy(['parent' => null]);
        $mediaImages = $this->mediaImagesRepository->findBy(['owner' => null]);
        $data = array_merge(
            (new MediaRootFolderNormalizer())->normalize($mediaFolders, null),
            (new MediaRootFolderNormalizer())->normalizeImage($mediaImages, null, ['request' => $request]));

        $paginate = $this->get('app.helper.paginate_helper')->paginateArray($data);

        return new JsonResponse($paginate);
    }


    /**
     * Create new folder
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $mediaFolder = new MediaFolder();
        $mediaFolder->setName($request->get('name', 'No name'));

        if ($parentId = $request->get('parent')) {
            $parent = $this->mediaFolderRepository->find($parentId);
            $mediaFolder->setParent($parent);
        }
        $this->mediaFolderRepository->add($mediaFolder);

        return new JsonResponse('Created', 201);
    }

    /**
     * Rename folder
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function edit(Request $request, $id)
    {
        /** @var MediaFolder $mediaFolder */
        if (!$mediaFolder = $this->mediaFolderRepository->find($id)) {
            return new JsonResponse('Folder not found', JsonResponse::HTTP_NOT_FOUND);
        }

        if ($name = $request->request->get('name')){
            $mediaFolder->setName($name);
            $this->mediaFolderRepository->add($mediaFolder);
        }
        return new JsonResponse('Updated');
    }


    /**
     * Move folders and images to folder
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function moveTo(Request $request, $id)
    {
        /** @var MediaFolder $mediaFolder */
        if (!$mediaFolder = $this->mediaFolderRepository->find($id)) {
            return new JsonResponse('Folder not found', JsonResponse::HTTP_NOT_FOUND);
        }
        if ($folders = $request->request->get('folders')) {
            $folders = $this->mediaFolderRepository->getByIds($folders);
            /** @var MediaFolder $folder */
            foreach ($folders as $folder) {
                $folder->setParent($mediaFolder);
                $this->mediaFolderRepository->add($folder);
            }
        }
        if ($images = $request->request->get('images')) {
            $images = $this->mediaImagesRepository->getByIds($images);
            /** @var MediaImage $image */
            foreach ($images as $image) {
                $image->setOwner($mediaFolder);
                $this->mediaImagesRepository->add($image);
            }
        }
        return new JsonResponse('Moved');
    }

    /**
     * Move folders and images to root folder
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function moveToRoot(Request $request)
    {

        if ($folders = $request->request->get('folders')) {
            $folders = $this->mediaFolderRepository->getByIds($folders);
            /** @var MediaFolder $folder */
            foreach ($folders as $folder) {
                $folder->setParent(null);
                $this->mediaFolderRepository->add($folder);
            }
        }
        if ($images = $request->request->get('images')) {
            $images = $this->mediaImagesRepository->getByIds($images);
            /** @var MediaImage $image */
            foreach ($images as $image) {
                $image->setOwner(null);
                $this->mediaImagesRepository->add($image);
            }
        }
        return new JsonResponse('Moved');
    }

    /**
     * Delete folder
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function delete(Request $request, $id)
    {
        /** @var MediaFolder $mediaFolder */
        if (!$mediaFolder = $this->mediaFolderRepository->find($id)) {
            return new JsonResponse('Folder not found', JsonResponse::HTTP_NOT_FOUND);
        }
        $this->mediaFolderRepository->remove($mediaFolder);
        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }

}
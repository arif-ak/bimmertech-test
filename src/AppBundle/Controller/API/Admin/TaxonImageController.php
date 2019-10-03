<?php

namespace AppBundle\Controller\API\Admin;

use AppBundle\Repository\TaxonRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ProductImage;
use Sylius\Component\Core\Model\TaxonImage;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TaxonImageController
 *
 * @package AppBundle\Controller\API\Admin
 */
class TaxonImageController extends Controller
{
    /**
     * @var TaxonRepository
     */
    private $taxonRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ImageUploaderInterface
     */
    private $imageUploader;

    /**
     * @var EntityRepository
     */
    private $taxonImageRepository;

    /**
     * TaxonImageController constructor.
     *
     * @param TaxonRepository $taxonRepository
     * @param ObjectManager $objectManager
     * @param ImageUploaderInterface $imageUploader
     * @param EntityRepository $taxonImageRepository
     */
    public function __construct(
        TaxonRepository $taxonRepository,
        ObjectManager $objectManager,
        ImageUploaderInterface $imageUploader,
        EntityRepository $taxonImageRepository
    )
    {
        $this->taxonRepository = $taxonRepository;
        $this->taxonImageRepository = $taxonImageRepository;
        $this->objectManager = $objectManager;
        $this->imageUploader = $imageUploader;
    }

    /**
     * Add images
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function addImages(Request $request, $id)
    {
        if (!$taxon = $this->taxonRepository->find($id)) {
            return new JsonResponse('Taxon not found', 404);
        }
        if (!$image = $request->files->get('img')) {
            return new JsonResponse('Bad parameter file', 400);
        }

        $imageId = $request->request->get('id') ?: 0;

        if (!$taxonImage = $this->taxonImageRepository->find($imageId)) {
            $taxonImage = new TaxonImage();
        }

        $taxonImage->setFile($image);
        $this->uploadImage($taxonImage, $taxon);

        $this->objectManager->persist($taxonImage);
        $this->objectManager->flush();

        return new JsonResponse(['id' => $taxonImage->getId()], 200);
    }

    /**
     * Remove product image
     *
     * @param $Id
     * @return JsonResponse
     */
    public function removeImages($Id)
    {
        /** @var TaxonImage $taxonImage */
        if (!$taxonImage = $this->taxonImageRepository->find($Id)) {
            return new  JsonResponse('Taxon image not found', 404);
        }

        $this->objectManager->remove($taxonImage);
        $this->objectManager->flush();

        return new JsonResponse('Images deleted', 204);
    }

    /**
     * Upload file
     *
     * @param $image
     * @param $taxon
     */
    public function uploadImage($image, $taxon)
    {
        /** @var ProductImage $image */
        if ($image->hasFile()) {
            $this->imageUploader->upload($image);
            $image->getOwner() ?: $image->setOwner($taxon);
        }
    }
}
<?php

namespace AppBundle\Controller\API\Admin;

use AppBundle\Repository\ProductRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ProductImage;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProductImageController
 *
 * @package AppBundle\Controller\API\Admin
 */
class ProductImageController extends Controller
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

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
    private $productImageRepository;

    /**
     * ProductImageController constructor.
     *
     * @param ProductRepository $productRepository
     * @param ObjectManager $objectManager
     * @param ImageUploaderInterface $imageUploader
     * @param EntityRepository $productImageRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        ObjectManager $objectManager,
        ImageUploaderInterface $imageUploader,
        EntityRepository $productImageRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->productImageRepository = $productImageRepository;
        $this->objectManager = $objectManager;
        $this->imageUploader = $imageUploader;
    }

    /**
     * Add images
     * @param Request $request
     * @param $productId
     * @return JsonResponse
     */
    public function addImages(Request $request, $productId)
    {
        if (!$product = $this->productRepository->find($productId)) {
            return new JsonResponse('Product not found', 404);
        }

        if (!$image = $request->files->get('img')) {
            return new JsonResponse('Bad parameter file', 400);
        }

        $imageId = $request->request->get('id') ?: 0;
        if (!$productImage = $this->productImageRepository->find($imageId)) {
            $productImage = new ProductImage();
        }

        $productImage->setFile($image);
        $this->uploadImage($productImage, $product);

//        $imagineCacheManager = $this->get('liip_imagine.cache.manager');
//        $image = $productImage->getPath();
//        $path = '/media/image/' . $image;
//        $imagineCacheManager->getBrowserPath('/relative/path/to/image.jpg', 'my_thumb');
//        $imagineCacheManager->getBrowserPath($path, 'sylius_admin_product_thumbnail');

        $this->objectManager->persist($productImage);
        $this->objectManager->flush();

        return new JsonResponse(['id' => $productImage->getId()], 200);
    }

    /**
     * Remove product image
     *
     * @param $Id
     * @return JsonResponse
     */
    public function removeImages($Id)
    {
        /** @var ProductImage $productImage */
        if (!$productImage = $this->productImageRepository->find($Id)) {
            return new  JsonResponse('Product image not found', 404);
        }

        $this->objectManager->remove($productImage);
        $this->objectManager->flush();

        return new JsonResponse('Images deleted', 204);
    }

    /**
     * Upload file
     *
     * @param $image
     * @param $product
     */
    public function uploadImage($image, $product)
    {
        /** @var ProductImage $image */
        if ($image->hasFile()) {
            $this->imageUploader->upload($image);
            $image->getOwner() ?: $image->setOwner($product);
        }
    }
}
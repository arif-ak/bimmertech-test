<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 26.02.2019
 * Time: 13:44
 */

namespace AppBundle\Fixture;

use AppBundle\Entity\BlogCategory;
use AppBundle\Entity\BlogPost;
use AppBundle\Entity\BlogPostImage;
use AppBundle\Service\ImageUploader;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BlogPostFixture extends AbstractFixture implements FixtureInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;


    /**
     * @var ImageUploader
     */
    private $imageUploader;


    /**
     * BlogPostFixture constructor.
     * @param ObjectManager $objectManager
     * @param ImageUploader $imageUploader
     */
    public function __construct(ObjectManager $objectManager, ImageUploader $imageUploader)
    {
        $this->objectManager = $objectManager;
        $this->imageUploader = $imageUploader;
    }

    /**
     * @param array $options
     */
    public function load(array $options): void
    {
        $blogPosts = $options['custom'];

        foreach ($blogPosts as $post){
           $blogPost = new BlogPost();
           $blogPost->setBlogCategory($this->addCategory($post));
           foreach ($post['posts'] as $item){
               $blogPost->setSlug($item['slug']);
               $blogPost->setEnabled($item['enabled']);
               $blogPost->setMetaKeywords($item['meta_keywords']);
               $blogPost->setMetaDescription($item['meta_description']);
               $blogPost->setTitle($item['title']);
               $blogPost->setMetaTags($item['meta_tags']);
               $blogPost->setContent($item['content']);

               foreach ($item['images'] as $image){
                   $blogPost->addBlogPostImage($this->addBlogPostImages($image));
               }
               $this->objectManager->persist($blogPost);
           }
        }

        $this->objectManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'blog_post';
    }


    /**
     * @param ArrayNodeDefinition $resourceNode
     */
    protected function configureOptionsNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
            ->arrayNode('custom')
            ->performNoDeepMerging()
            ->prototype('array')
            ->children()
            ->scalarNode('name')->end()
            ->scalarNode('enabled')->end()
            ->arrayNode('posts')->prototype('array')
            ->children()
            ->scalarNode('slug')->end()
            ->scalarNode('enabled')->end()
            ->scalarNode('meta_keywords')->end()
            ->scalarNode('meta_description')->end()
            ->scalarNode('title')->end()
            ->scalarNode('meta_tags')->end()
            ->scalarNode('content')->end()
            ->arrayNode('images')->prototype('array')
            ->children()
            ->scalarNode('aspect_ratio')->end()
            ->scalarNode('path')->end()
            ->end()
            ->end()
            ->end();
    }

    /**
     * @param array $post
     * @return BlogCategory
     */
    public function addCategory(array $post){
        $blogCategory = new BlogCategory();
        $blogCategory->setName($post['name']);
        $blogCategory->setEnabled($post['enabled']);
        $this->objectManager->persist($blogCategory);

        $this->objectManager->flush();
        return $blogCategory;
    }

    /**
     * @param array $image
     * @return BlogPostImage
     */
    public function addBlogPostImages(array $image){
        $blogPostImage = new BlogPostImage();
        $blogPostImage->setAspectRatio($image['aspect_ratio']);

        $blogPostImage = $this->createImage($blogPostImage, $image['path']);

        return $blogPostImage;
    }

    /**
     * @param BlogPostImage $blogPostImage
     * @param $imagePost
     * @return BlogPostImage
     */
    private function createImage(BlogPostImage $blogPostImage,$imagePost)
    {
        $file = new UploadedFile(__DIR__ . '/../../..' . $imagePost, basename($imagePost));

        $blogPostImage->setFile($file);
        $this->imageUploader->upload($blogPostImage);

        return $blogPostImage;
    }
}
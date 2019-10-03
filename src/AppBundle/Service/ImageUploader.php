<?php

namespace AppBundle\Service;

use Gaufrette\Filesystem;
use Sylius\Component\Core\Model\ImageInterface;
use Gaufrette\Adapter\Local as LocalAdapter;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\File;
use Webmozart\Assert\Assert;

class ImageUploader
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param Container $container
     * @throws \Exception
     */
    public function __construct(Container $container)
    {
        $rootDir = $container->get('kernel')->getProjectDir();
        $adapter = new LocalAdapter($rootDir . "/web/media/image");
        $this->filesystem = new Filesystem($adapter);
    }

    /**
     * {@inheritdoc}
     */
    public function upload($image): void
    {
        if (!$image->hasFile()) {
            return;
        }

        $file = $image->getFile();

        /** @var File $file */
        Assert::isInstanceOf($file, File::class);

        do {
            $hash = bin2hex(random_bytes(16));
            $path = $this->expandPath($hash . '.' . $file->guessExtension());
        } while ($this->filesystem->has($path));

        $image->setPath($path);

        $this->filesystem->write(
            $image->getPath(),
            file_get_contents($image->getFile()->getPathname())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function remove($path): bool
    {
        if ($this->filesystem->has($path)) {
            return $this->filesystem->delete($path);
        }

        return false;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function expandPath(string $path): string
    {
        return sprintf(
            '%s/%s/%s',
            substr($path, 0, 2),
            substr($path, 2, 2),
            substr($path, 4)
        );
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function has(string $path): bool
    {
        return $this->filesystem->has($path);
    }
}

<?php

namespace AppBundle\Twig;

/**
 * Class PopupOptionExtension
 * @package AppBundle\Twig
 */
class BlogPostContentExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new \Twig_Filter('postContent', [$this, 'postContentFilter']),
        ];
    }

    /**
     * @param $option
     * @return string
     */
    public function postContentFilter($option): string
    {
        if (!empty($option)) {
            $replaysCustomTags = str_replace('custom-', '', $option);

            return $replaysCustomTags;
        } else {
            return (string)"";
        }
    }
}

<?php

namespace AppBundle\Twig;

use Sylius\Component\Addressing\Model\CountryInterface;
use Symfony\Component\Intl\Intl;

/**
 * Class CountryNameExtension
 * @package AppBundle\Twig
 */
class CountryNameExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new \Twig_Filter('sylius_country_name', [$this, 'translateCountryIsoCode']),
        ];
    }

    /**
     * @param mixed $country
     * @param string|null $locale
     *
     * @return string
     */
    public function translateCountryIsoCode($country, ?string $locale = null): string
    {
        if (!empty($country)) {

            if ($country instanceof CountryInterface) {
                return Intl::getRegionBundle()->getCountryName($country->getCode(), $locale);
            }

            return Intl::getRegionBundle()->getCountryName($country, $locale);
        } else {
            return "N/A";
        }
    }
}
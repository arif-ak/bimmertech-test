<?php

namespace AppBundle\Twig;

use AppBundle\Entity\PopupOption;

/**
 * Class PopupOptionExtension
 * @package AppBundle\Twig
 */
class PopupOptionExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new \Twig_Filter('popup', [$this, 'popupFilter']),
        ];
    }

    /**
     * @param $option
     * @param $objects
     * @param bool $addHTMLElement
     * @return string
     */
    public function popupFilter($option, $objects = null, $addHTMLElement = false): string
    {
        if (!empty($option)) {
            if (count($objects) >= 1) {
                $filteredOption = $option;

                // add html element if content from DB are string
                if ($addHTMLElement == true) {
                    $filteredOption = '<span>' . $filteredOption . '</span>';
                }

                /** @var PopupOption $object */
                foreach ($objects as $key => $object) {
                    if (strpos($option, $object->getTitle()) !== false) {
                        $title = $object->getTitle();
                        $description = $object->getDescription();

                        if (empty($object->getVinCheckServiceId())) {
                            $contentOfReplace =
                                "<section-popup on='hover' distance='3' inline-template>
                                <span>
                                    <span :id='buttonId' class='popup bt-popup'>$title</span>";
                            $popupContentWithoutCompatibility = "
                                    <div :id='popupId' class='customPopup ui popup transition hidden'>
                                            <div class='content popup-option-font-color'>
                                                <span>$description</span>
                                            </div>
                                    </div>
                                </span>
                            </section-popup>";
                        } else {
                            $contentOfReplace =
                                "<section-popup on='hoverable' distance='3' inline-template>
                                <span>
                                    <span :id='buttonId' class='popup bt-popup'>$title</span>";
                            $popupContentWithoutCompatibility = "
                                    <div :id='popupId' class='customPopup ui popup transition hidden'>
                                        <div class='content popup-option-font-color'>
                                            <p>$description</p>                                           
                                            <span class='popup-option' v-if=\"product\">
                                                    <div class='popup-option-content' v-if=\"product.popup_option[$key].compatibility == true\">                                                       
                                                        <div class=\"icon-width bt-round-icon green\">
                                                            <div class=\"compatibility bt-icon round bt-yes\"></div>
                                                        </div>
                                                        <div> Available in your [[vincheck]] </div> 
                                                    </div>   
                                                        <div class='popup-option-content' v-else-if=\"product.popup_option[$key].compatibility == false\">                                                                                                         <div class=\"icon-width bt-round-icon red\">
                                                            <div class=\"compatibility bt-icon round bt-no\"></div>
                                                        </div>
                                                        <div>Not available in your [[vincheck]]</div>
                                                    </div>
                                                    <div class='popup-option-content' v-else-if=\"product.popup_option[$key].compatibility == null \">            
                                                        <div class=\"icon-width bt-round-icon yellow\">
                                                            <div class=\"compatibility bt-icon round bt-attention\"></div>
                                                        </div>
                                                        <div><span @click=\"openVinchek()\" class=\"vin-check-message-blue\">Enter your VIN</span> to check if your car has it</div>
                                                    </div>
                                            </span>
                                        </div>
                                    </div>
                                </span>
                            </section-popup>";
                        }

                        $replace = str_replace($title, $contentOfReplace . $popupContentWithoutCompatibility, $filteredOption);
                        $replaysTagOpenFormBlock = str_replace('<p>', '<div class="margin-bottom-1">', $replace);
                        $replaysTagOpenFormBlockSecond = str_replace('<p dir="ltr"', '<div class="margin-bottom-1"', $replaysTagOpenFormBlock);
                        $replaysTagClosedFormBlock = str_replace('</p>', '</div>', $replaysTagOpenFormBlockSecond);

                        $filteredOption = $replaysTagClosedFormBlock;
                    }
                }

                return $filteredOption;
            } else {
                return !empty($option) ? $option : "";
            }
        } else {
            return (string)"";
        }
    }
}

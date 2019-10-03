<?php

namespace AppBundle\Utils;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;

trait ValidationTrait
{
    /**
     * @var array
     */
    private static $constraintCodes = [
        NotBlank::class => 101,
        Length::class => 110,
        Regex::class => 201,
        Email::class => 202,
        DateTime::class => 203,
        Range::class => 204,
        UniqueEntity::class => 601,
    ];

    /**
     * Create response with validation errors
     *
     * @param FormInterface $form
     *
     * @return JsonResponse
     */
    protected function createValidationErrorsResponse(FormInterface $form)
    {
        return new JsonResponse(['errors' => $this->getErrorsFromForm($form)], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    private function getErrorsFromForm(FormInterface $form): array
    {
        // Errors container
        $errors = [];

        foreach ($form->getErrors() as $item) {
            $error = [
                'code' => 0,
                'message' => $item->getMessage(),
            ];
            if ($item->getCause()->getConstraint() &&
                array_key_exists(get_class($item->getCause()->getConstraint()), self::$constraintCodes)) {
                $error['code'] = self::$constraintCodes[get_class($item->getCause()->getConstraint())];
            }

            $errors[] = $error;
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    foreach ($childErrors as $errorMessage) {
                        if (isset($errorMessage['message'])) {
                            $message = $errorMessage['message'];
                        } elseif (isset($errorMessage['details'])) {
                            $message = $errorMessage['details'];
                        }

                        $errors[] = [
                            'code' => $errorMessage['code'],
                            'source' => $childForm->getName(),
                            'details' => isset($message) ? $message : "" ,
                        ];
                    }
                }
            }
        }

        return $errors;
    }

    public function getErrorResponse($message, $code = 400)
    {
        $error = [
            'code' => $code,
            'message' => $message
        ];

        return new JsonResponse(["errors" => $error], $code);
    }
}

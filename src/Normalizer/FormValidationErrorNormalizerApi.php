<?php

namespace App\Normalizer;

use App\Exception\FormValidationErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;

class FormValidationErrorNormalizerApi extends ApiAbstractNormalizer
{
    public function normalize($object, string $format = null, array $context = []): Response
    {
        $violations = [];

        /** @var ConstraintViolation $violation */
        foreach ($object->getConstraintViolationList() as $violation) {
            $violations[$violation->getPropertyPath()] = array_unique(array_merge($violations[$violation->getPropertyPath()] ?? [], [$violation->getMessage()]));
        }

        $result = [
            'title' => 'Validation Failed',
        ];
        return new JsonResponse($result + ['errors' => $violations]);
    }


    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof FormValidationErrorException;
    }
}

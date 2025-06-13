<?php

namespace Paymongo\Exceptions;

use Paymongo\Entities\Error;

/**
 * The default exception thrown for a generic API error.
 * Extends BaseException and adds the ability to filter errors.
 */
class ApiException extends BaseException
{
    /**
     * Returns an array of Error objects from the API response.
     *
     * This method overrides the parent `getErrors()` to add the special
     * ability to filter the errors by a source attribute.
     *
     * @param string $attribute Optionally filter errors by a specific source attribute.
     * @return Error[]
     */
    public function getErrors(string $attribute = ''): array
    {
        // First, get all the error objects from the parent BaseException.
        $allErrors = parent::getErrors();

        // If no attribute is specified for filtering, return all the errors.
        if ($attribute === '') {
            return $allErrors;
        }

        // Otherwise, filter the errors and return the result.
        return array_filter(
            $allErrors,
            fn(Error $error) => $error->hasSource() && $error->source->attribute === $attribute
        );
    }
}

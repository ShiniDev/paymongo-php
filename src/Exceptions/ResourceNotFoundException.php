<?php

namespace Paymongo\Exceptions;

use Paymongo\Entities\Error;

/**
 * Thrown when a specific resource could not be found (HTTP 404).
 */
class ResourceNotFoundException extends BaseException
{
    /**
     * Gets the first error from the error response.
     *
     * @return Error|null The first Error object, or null if there are no errors.
     */
    public function getError(): ?Error
    {
        $errors = $this->getErrors(); // Get all errors from parent

        return $errors[0] ?? null; // Safely return the first one, or null.
    }
}

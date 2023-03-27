<?php

declare(strict_types=1);

namespace Jayrods\ScubaPHP\Controller\Validation;

use Jayrods\ScubaPHP\Core\Request;

interface Validator
{
    /**
     * 
     */
    public function validate(Request $request): bool;
}

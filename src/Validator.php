<?php declare(strict_types=1);

namespace Frodo;

use Frodo\Exception\ValidationException;

interface Validator
{

    /**
     * @param mixed $raw
     */
    public function __construct($raw, bool $is_required);

    /** @throws ValidationException */
    public function validate();
}

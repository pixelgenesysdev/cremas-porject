<?php

namespace FSVendor\WPDesk\Forms\Sanitizer;

use FSVendor\WPDesk\Forms\Sanitizer;
class NoSanitize implements Sanitizer
{
    public function sanitize($value)
    {
        return $value;
    }
}

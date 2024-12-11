<?php

namespace FSProVendor\WPDesk\Forms\Sanitizer;

use FSProVendor\WPDesk\Forms\Sanitizer;
class TextFieldSanitizer implements Sanitizer
{
    public function sanitize($value)
    {
        return sanitize_text_field($value);
    }
}

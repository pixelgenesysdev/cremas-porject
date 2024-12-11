<?php

namespace FSProVendor\WPDesk\Forms\Validator;

use FSProVendor\WPDesk\Forms\Validator;
class RequiredValidator implements Validator
{
    public function is_valid($value)
    {
        return $value !== null;
    }
    public function get_messages()
    {
        return [];
    }
}

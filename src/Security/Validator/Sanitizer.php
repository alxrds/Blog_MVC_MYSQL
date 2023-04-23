<?php

    namespace App\Security\Validator;

    class Sanitizer
    {
        public static function sanitizeData($data, $sanitizerFilters)
        {
            return filter_var_array($data, $sanitizerFilters);
        }
    }
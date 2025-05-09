<?php

namespace Hexlet\Code;

use Valitron\Validator;

use function PHPUnit\Framework\isNull;

class UrlValidator
{
    public function validate($urlData): array
    {
        $errors = [];
        $urlName = $urlData['name'];

        if (empty($urlName)) {
            $errors[] = 'URL не должен быть пустым';
        }

        if (!filter_var($urlName, FILTER_VALIDATE_URL) || strlen($urlName) > 255) {
            $errors[] = 'Некорректный URL';
        }

        return $errors;
    }
}

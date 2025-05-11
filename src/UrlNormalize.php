<?php

namespace Hexlet\Code;

class UrlNormalize
{
    public function normalize(string $name): string
    {
        $parsed = parse_url($name);
        $scheme = $parsed['scheme'] ?? '';
        $host = $parsed['host'] ?? '';
        $result = "{$scheme}://{$host}";

        if ($parsed === false) {
            return strtolower($name);
        }

        return strtolower($result);
    }
}

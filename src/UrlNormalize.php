<?php

namespace Hexlet\Code;

class UrlNormalize
{
    public function normalize(string $name): string
    {
        $parsed = parse_url($name);
        $result = "{$parsed['scheme']}://{$parsed['host']}";

        if ($parsed === false) {
            return $name;
        }
        
        return $result;
    }
}

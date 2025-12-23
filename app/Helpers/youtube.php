<?php

if (!function_exists('youtube_embed_url')) {
    function youtube_embed_url(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        preg_match(
            '%(?:youtube(?:-nocookie)?\.com/(?:.*[?&]v=|(?:v|e(?:mbed)?)/)|youtu\.be/)([^"&?/ ]{11})%i',
            $url,
            $matches
        );

        return isset($matches[1])
            ? 'https://www.youtube.com/embed/' . $matches[1]
            : null;
    }
}

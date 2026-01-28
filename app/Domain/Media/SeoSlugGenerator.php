<?php

namespace App\Domain\Media;

use Illuminate\Support\Str;

class SeoSlugGenerator
{
    public function generate(?string $title, ?string $altText, string $uuid): string
    {
        $source = $title ?? $altText;

        if (empty($source)) {
            return '';
        }

        $slug = $this->toSlug($source);

        if (empty($slug)) {
            return '';
        }

        $shortUuid = substr($uuid, 0, 8);

        return "{$slug}-{$shortUuid}";
    }

    public function generateFileName(?string $title, ?string $altText, string $uuid, string $extension): string
    {
        $seoSlug = $this->generate($title, $altText, $uuid);

        if (empty($seoSlug)) {
            return '';
        }

        $extension = ltrim(strtolower($extension), '.');

        return "{$seoSlug}.{$extension}";
    }

    public function toSlug(string $text): string
    {
        $text = $this->transliterate($text);

        $text = Str::ascii($text);

        $text = preg_replace('/[^a-zA-Z0-9\s-]/', '', $text);

        $text = preg_replace('/[\s_]+/', '-', $text);

        $text = preg_replace('/-+/', '-', $text);

        $text = trim($text, '-');

        $text = strtolower($text);

        if (strlen($text) > 60) {
            $text = substr($text, 0, 60);
            $text = preg_replace('/-[^-]*$/', '', $text);
        }

        return $text;
    }

    private function transliterate(string $text): string
    {
        $map = [
            'á' => 'a', 'č' => 'c', 'ď' => 'd', 'é' => 'e', 'ě' => 'e',
            'í' => 'i', 'ň' => 'n', 'ó' => 'o', 'ř' => 'r', 'š' => 's',
            'ť' => 't', 'ú' => 'u', 'ů' => 'u', 'ý' => 'y', 'ž' => 'z',
            'Á' => 'A', 'Č' => 'C', 'Ď' => 'D', 'É' => 'E', 'Ě' => 'E',
            'Í' => 'I', 'Ň' => 'N', 'Ó' => 'O', 'Ř' => 'R', 'Š' => 'S',
            'Ť' => 'T', 'Ú' => 'U', 'Ů' => 'U', 'Ý' => 'Y', 'Ž' => 'Z',
            'ä' => 'a', 'ö' => 'o', 'ü' => 'u', 'ß' => 'ss',
            'Ä' => 'A', 'Ö' => 'O', 'Ü' => 'U',
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n',
            'ś' => 's', 'ź' => 'z', 'ż' => 'z',
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'E', 'Ł' => 'L', 'Ń' => 'N',
            'Ś' => 'S', 'Ź' => 'Z', 'Ż' => 'Z',
        ];

        return strtr($text, $map);
    }
}

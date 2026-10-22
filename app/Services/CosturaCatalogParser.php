<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use RuntimeException;

class CosturaCatalogParser
{
    public const ORIGIN = 'https://www.costura.pt';

    // Custom "no photo" artwork observed on costura.pt product detail pages (512px PNG).
    public const NO_PHOTO_SHA256 = 'efc2fd772e31c470f1696be93219390b07c1830461fd73aa7014bdabaf960871';

    public static function isPlaceholder(string $bytes, string $odooPlaceholder): bool
    {
        return in_array(hash('sha256', $bytes), [self::NO_PHOTO_SHA256, hash('sha256', $odooPlaceholder)], true);
    }

    public static function cleanName(string $name): string
    {
        return trim(preg_replace('/^\s*\[[^\]]+\]\s*/u', '', $name));
    }

    public static function brand(string $name): ?string
    {
        foreach (['Juki', 'Brother', 'Singer', 'Jack', 'Siruba', 'Yamato', 'Pegasus', 'Seiko', 'Durkopp Adler', 'Global', 'Schmetz', 'Organ', 'Groz-Beckert', 'Aitu'] as $brand) {
            if (preg_match('/\\b'.preg_quote($brand, '/').'\\b/iu', $name)) {
                return $brand;
            }
        }

        return null;
    }

    public function document(string $html): DOMXPath
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8">'.$html);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        return new DOMXPath($document);
    }

    public function url(string $value): string
    {
        $url = str_starts_with($value, '/') && ! str_starts_with($value, '//') ? self::ORIGIN.$value : $value;
        if (parse_url($url, PHP_URL_SCHEME) !== 'https' || parse_url($url, PHP_URL_HOST) !== 'www.costura.pt') {
            throw new RuntimeException('URL da fonte inválido.');
        }

        return $url;
    }

    public function listing(string $html, string $categoryPath): array
    {
        $xpath = $this->document($html);
        $products = [];
        foreach ($xpath->query('//form[contains(concat(" ", normalize-space(@class), " "), " oe_product_cart ")]') as $card) {
            $link = $xpath->query('.//a[@itemprop="name"]', $card)->item(0);
            if (! $link) {
                continue;
            }
            $products[] = [
                'name' => self::cleanName($link->getAttribute('content') ?: $link->textContent),
                'url' => $this->url($link->getAttribute('href')),
                'has_image' => $xpath->query('.//img[@itemprop="image"]', $card)->length > 0,
            ];
        }
        if (! $products) {
            throw new RuntimeException('Listagem vazia ou HTML inesperado; verificar seletores.');
        }
        $pages = [];
        foreach ($xpath->query('//a[contains(concat(" ", normalize-space(@class), " "), " page-link ")][@href]') as $link) {
            $path = parse_url($link->getAttribute('href'), PHP_URL_PATH);
            if ($path === $categoryPath || preg_match('#^'.preg_quote($categoryPath, '#').'/page/[0-9]+$#', $path ?? '')) {
                $pages[] = $this->url($link->getAttribute('href'));
            }
        }

        return ['products' => $products, 'pages' => array_unique($pages)];
    }

    public function image(string $html): ?string
    {
        $xpath = $this->document($html);
        $image = $xpath->query('//img[contains(concat(" ", normalize-space(@class), " "), " product_detail_img ")]')->item(0);
        if (! $image) {
            return null;
        }
        $src = $image->getAttribute('data-zoom-image') ?: $image->getAttribute('src');
        if (! $src || str_contains($src, 'placeholder')) {
            return null;
        }

        return $this->url($src);
    }
}

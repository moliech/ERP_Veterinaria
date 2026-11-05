<?php

declare(strict_types=1);

namespace App\Services\Theme;

use InvalidArgumentException;

final class ThemeContrastService
{
    /**
     * Genera la parametrización de tokens corporativos con evaluación de contraste WCAG 2.1.
     *
     * @return array{
     *     primitive: string,
     *     brand_primary: string,
     *     brand_primary_hover: string,
     *     brand_contrast_text: string,
     *     ratio_against_white: float,
     *     wcag_aa_compliant: bool
     * }
     */
    public function buildBrandTheme(string $primitiveHex): array
    {
        $hex = $this->normalizeHex($primitiveHex);
        $ratioVsWhite = $this->getContrastRatio($hex, '#FFFFFF');

        $optimalTextOnBrand = $ratioVsWhite >= 4.5 ? '#FFFFFF' : '#111111';
        $hoverHex = $this->adjustLuminance($hex, -0.15);

        return [
            'primitive'           => $hex,
            'brand_primary'       => $hex,
            'brand_primary_hover' => $hoverHex,
            'brand_contrast_text' => $optimalTextOnBrand,
            'ratio_against_white' => round($ratioVsWhite, 2),
            'wcag_aa_compliant'   => $ratioVsWhite >= 4.5,
        ];
    }

    /**
     * Calcula la luminancia relativa en el espacio sRGB linealizado según WCAG 2.1.
     */
    public function getRelativeLuminance(string $hex): float
    {
        [$r, $g, $b] = $this->hexToRgbNormalized($hex);
        return (0.2126 * $this->linearize($r)) + (0.7152 * $this->linearize($g)) + (0.0722 * $this->linearize($b));
    }

    /**
     * Calcula la razón de contraste dimensional (L1 + 0.05) / (L2 + 0.05).
     */
    public function getContrastRatio(string $hex1, string $hex2): float
    {
        $l1 = max($this->getRelativeLuminance($hex1), $this->getRelativeLuminance($hex2));
        $l2 = min($this->getRelativeLuminance($hex1), $this->getRelativeLuminance($hex2));
        return ($l1 + 0.05) / ($l2 + 0.05);
    }

    private function linearize(float $c): float
    {
        return $c <= 0.04045 ? $c / 12.92 : pow(($c + 0.055) / 1.055, 2.4);
    }

    private function normalizeHex(string $hex): string
    {
        $clean = ltrim(trim($hex), '#');
        if (strlen($clean) === 3) {
            $clean = $clean[0].$clean[0].$clean[1].$clean[1].$clean[2].$clean[2];
        }
        if (! preg_match('/^[0-9A-Fa-f]{6}$/', $clean)) {
            throw new InvalidArgumentException("Formato hexadecimal inválido: {$hex}");
        }
        return '#' . strtoupper($clean);
    }

    private function hexToRgbNormalized(string $hex): array
    {
        $clean = ltrim($this->normalizeHex($hex), '#');
        return [
            hexdec(substr($clean, 0, 2)) / 255.0,
            hexdec(substr($clean, 2, 2)) / 255.0,
            hexdec(substr($clean, 4, 2)) / 255.0,
        ];
    }

    private function adjustLuminance(string $hex, float $delta): string
    {
        [$r, $g, $b] = $this->hexToRgbNormalized($hex);
        $factor = 1 + $delta;
        return sprintf(
            '#%02X%02X%02X',
            (int) max(0, min(255, round($r * 255 * $factor))),
            (int) max(0, min(255, round($g * 255 * $factor))),
            (int) max(0, min(255, round($b * 255 * $factor)))
        );
    }
}

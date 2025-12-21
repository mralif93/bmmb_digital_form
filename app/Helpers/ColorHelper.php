<?php

namespace App\Helpers;

class ColorHelper
{
    /**
     * Generate color shades from a primary color hex value
     * Returns an array of shades from 50 (lightest) to 900 (darkest)
     */
    public static function generateColorShades(string $hexColor): array
    {
        // Remove # if present
        $hexColor = ltrim($hexColor, '#');
        
        // Convert 3-digit hex to 6-digit
        if (strlen($hexColor) === 3) {
            $hexColor = $hexColor[0] . $hexColor[0] . $hexColor[1] . $hexColor[1] . $hexColor[2] . $hexColor[2];
        }
        
        // Convert hex to RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        
        // Generate shades using HSL-like approach for better results
        $shades = [];
        
        // Light shades (50-400): Mix with white
        $lightFactors = [
            50 => 0.95,
            100 => 0.90,
            200 => 0.80,
            300 => 0.60,
            400 => 0.40,
        ];
        
        foreach ($lightFactors as $shade => $factor) {
            $newR = round($r + (255 - $r) * $factor);
            $newG = round($g + (255 - $g) * $factor);
            $newB = round($b + (255 - $b) * $factor);
            $shades[$shade] = sprintf('#%02x%02x%02x', $newR, $newG, $newB);
        }
        
        // Base color (500)
        $shades[500] = '#' . $hexColor;
        
        // Dark shades (600-900): Darken the base color
        $darkFactors = [
            600 => 0.15,
            700 => 0.30,
            800 => 0.50,
            900 => 0.70,
        ];
        
        foreach ($darkFactors as $shade => $factor) {
            $newR = max(0, round($r * (1 - $factor)));
            $newG = max(0, round($g * (1 - $factor)));
            $newB = max(0, round($b * (1 - $factor)));
            $shades[$shade] = sprintf('#%02x%02x%02x', $newR, $newG, $newB);
        }
        
        return $shades;
    }
}


<?php

namespace App\Helpers;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleTranslateHelper
{
    public static function translate(string $text, string $target = 'en', string $source = 'en'): string
    {
        if (empty($text)) {
            return $text;
        }

        // Skip translation if target is English
        if ($target === 'en') {
            return $text;
        }

        $cacheKey = "translation_{$source}_{$target}_" . md5($text);

        // Check cache first
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $tr = new GoogleTranslate();
            $tr->setSource($source);
            $tr->setTarget($target);

            $translatedText = $tr->translate($text);

            if ($translatedText) {
                // Cache the result
                Cache::put($cacheKey, $translatedText, now()->addDays(100));
                Log::info('Google Translate Success: ' . $text . ' => ' . $translatedText);
                return $translatedText;
            }

            return $text;
        } catch (\Exception $e) {
            Log::error('Google Translate Error: ' . $e->getMessage() . ' | Text: ' . $text . ' | Target: ' . $target);
            return $text; // Return original text if translation fails
        }
    }
}

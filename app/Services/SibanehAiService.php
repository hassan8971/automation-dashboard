<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SibanehAiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';
    // protected $geminiModel = 'gemini-flash-latest';

    public function __construct()
    {
        // مطمئن شوید که در فایل .env کلید GEMINI_API_KEY را ست کرده‌اید
        $this->apiKey = env('GEMINI_API_KEY');
    }

    /**
     * متد اصلی برای تولید متن با استفاده از جمنای
     *
     * @param string $prompt متن ورودی کاربر
     * @param string $model مدل مورد استفاده 
     * @param string|null $systemInstruction دستورالعمل سیستمی (پرسونا یا قوانین)
     * @return string|null
     */
    public function generateText(string $prompt, string $model = 'gemini-flash-latest', ?string $systemInstruction = null)
    {
        $url = "{$this->baseUrl}{$model}:generateContent?key={$this->apiKey}";

        // آماده‌سازی پیلود درخواست
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            // تنظیمات خلاقیت (اختیاری)
            'generationConfig' => [
                'temperature' => 0.7, 
            ]
        ];

        // افزودن دستورالعمل سیستمی (Fine-tuning context)
        if ($systemInstruction) {
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->failed()) {
                Log::error('SibanehAI Error: ' . $response->body());
                return null;
            }

            $data = $response->json();

            // استخراج متن از پاسخ JSON پیچیده گوگل
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        } catch (\Exception $e) {
            Log::error('SibanehAI Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ابزار کمکی برای ترجمه متون
     */
    public function translate(string $text, string $targetLang, $model = 'gemini-flash-latest')
    {
        $systemInstruction = "You are a professional translator. Translate the following text into Persian. Only return the translated text, no explanations.";
        return $this->generateText($text, $model, $systemInstruction);
    }

    /**
     * ابزار کمکی برای بهبود جستجو (استخراج کلمات کلیدی)
     */
    public function refineSearchQuery(string $userQuery, $model = 'gemini-flash-latest')
    {
        $systemInstruction = "You are a search engine optimizer. I will give you a messy user search query. You must extract the core intent and keywords. Return ONLY a JSON array of keywords. Example input: 'I want cheap running shoes red', Output: ['running shoes', 'red', 'cheap'].";
        
        $result = $this->generateText($userQuery, $model, $systemInstruction);
        
        // پاکسازی خروجی اگر مدل کدبلاک اضافه کرد
        if ($result) {
            $cleanJson = str_replace(['```json', '```'], '', $result);
            return json_decode($cleanJson, true) ?? []; 
        }
        
        return [];
    }
}
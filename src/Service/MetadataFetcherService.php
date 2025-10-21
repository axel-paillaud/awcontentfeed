<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Axelweb <contact@axelweb.fr>
 * @copyright 2025 Axelweb
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

declare(strict_types=1);

namespace Axelweb\AwContentFeed\Service;

class MetadataFetcherService
{
    /**
     * Fetch metadata from a URL based on content type
     *
     * @param string $url Content URL
     * @param string $type Content type (youtube or wordpress)
     * @return array Metadata (title, description, thumbnail)
     */
    public function fetch(string $url, string $type): array
    {
        if ($type === 'youtube') {
            return $this->fetchYoutubeMetadata($url);
        }

        if ($type === 'wordpress') {
            return $this->fetchWordPressMetadata($url);
        }

        return [
            'title' => null,
            'description' => null,
            'thumbnail' => null,
        ];
    }

    /**
     * Fetch YouTube video metadata using oEmbed API
     *
     * @param string $url YouTube video URL
     * @return array Metadata
     */
    public function fetchYoutubeMetadata(string $url): array
    {
        try {
            // YouTube oEmbed endpoint (no API key required)
            $oembedUrl = 'https://www.youtube.com/oembed?url=' . urlencode($url) . '&format=json';

            // Fetch data
            $response = $this->fetchUrl($oembedUrl);

            if (!$response) {
                return $this->getEmptyMetadata();
            }

            $data = json_decode($response, true);

            if (!$data || !isset($data['title'])) {
                return $this->getEmptyMetadata();
            }

            // Extract video ID for high-quality thumbnail
            $videoId = $this->extractYoutubeVideoId($url);
            $thumbnail = $videoId 
                ? "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg"
                : ($data['thumbnail_url'] ?? null);

            return [
                'title' => $data['title'] ?? null,
                'description' => $data['author_name'] ?? null, // Channel name as description
                'thumbnail' => $thumbnail,
            ];
        } catch (\Exception $e) {
            return $this->getEmptyMetadata();
        }
    }

    /**
     * Fetch WordPress article metadata using Open Graph tags
     *
     * @param string $url WordPress article URL
     * @return array Metadata
     */
    public function fetchWordPressMetadata(string $url): array
    {
        try {
            // Fetch HTML content
            $html = $this->fetchUrl($url);

            if (!$html) {
                return $this->getEmptyMetadata();
            }

            // Parse Open Graph tags
            $title = $this->extractMetaTag($html, 'og:title');
            $description = $this->extractMetaTag($html, 'og:description');
            $thumbnail = $this->extractMetaTag($html, 'og:image');

            // Fallback to standard meta tags if OG tags not found
            if (!$title) {
                $title = $this->extractMetaTag($html, 'title', false);
            }

            if (!$description) {
                $description = $this->extractMetaTag($html, 'description', false);
            }

            return [
                'title' => $title,
                'description' => $description ? $this->truncateText($description, 500) : null,
                'thumbnail' => $thumbnail,
            ];
        } catch (\Exception $e) {
            return $this->getEmptyMetadata();
        }
    }

    /**
     * Extract YouTube video ID from URL
     *
     * @param string $url YouTube URL
     * @return string|null Video ID
     */
    private function extractYoutubeVideoId(string $url): ?string
    {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
        
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Extract meta tag content from HTML
     *
     * @param string $html HTML content
     * @param string $property Meta property name
     * @param bool $isOgTag Whether it's an Open Graph tag
     * @return string|null Meta tag content
     */
    private function extractMetaTag(string $html, string $property, bool $isOgTag = true): ?string
    {
        if ($isOgTag) {
            // Open Graph tag: <meta property="og:title" content="...">
            $pattern = '/<meta\s+property=["\']og:' . preg_quote($property, '/') . '["\'].*?content=["\'](.*?)["\']/i';
        } else {
            // Standard meta tag: <meta name="description" content="...">
            $pattern = '/<meta\s+name=["\']' . preg_quote($property, '/') . '["\'].*?content=["\'](.*?)["\']/i';
        }

        if (preg_match($pattern, $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }

        return null;
    }

    /**
     * Fetch URL content using cURL or file_get_contents
     *
     * @param string $url URL to fetch
     * @return string|false URL content or false on failure
     */
    private function fetchUrl(string $url)
    {
        // Try cURL first (more reliable)
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; AwContentFeed/1.0)');
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                return $response;
            }
        }

        // Fallback to file_get_contents
        if (ini_get('allow_url_fopen')) {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Mozilla/5.0 (compatible; AwContentFeed/1.0)',
                ],
            ]);

            return @file_get_contents($url, false, $context);
        }

        return false;
    }

    /**
     * Get empty metadata array
     *
     * @return array
     */
    private function getEmptyMetadata(): array
    {
        return [
            'title' => null,
            'description' => null,
            'thumbnail' => null,
        ];
    }

    /**
     * Truncate text to specified length
     *
     * @param string $text Text to truncate
     * @param int $length Maximum length
     * @return string Truncated text
     */
    private function truncateText(string $text, int $length): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . '...';
    }
}

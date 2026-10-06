<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Polyfill para funções mbstring em hosting sem a extensão.
 * Usa funções nativas PHP (compatíveis com UTF-8 para texto PT/ASCII).
 * Carregar ANTES de qualquer classe que use mb_*.
 */
final class MbstringPolyfill
{
    public static function carregar(): void
    {
        if (extension_loaded('mbstring')) {
            return;
        }

        // mb_strlen -> strlen (UTF-8 safe para texto maioritariamente ASCII/PT)
        if (!function_exists('mb_strlen')) {
            function mb_strlen(string $string, ?string $encoding = null): int
            {
                return strlen($string);
            }
        }

        // mb_substr -> substr
        if (!function_exists('mb_substr')) {
            function mb_substr(string $string, int $offset, ?int $length = null, ?string $encoding = null): string
            {
                return $length === null ? substr($string, $offset) : substr($string, $offset, $length);
            }
        }

        // mb_strtolower -> strtolower (UTF-8 safe para ASCII/PT)
        if (!function_exists('mb_strtolower')) {
            function mb_strtolower(string $string, ?string $encoding = null): string
            {
                return strtolower($string);
            }
        }

        // mb_strimwidth -> implementação simples
        if (!function_exists('mb_strimwidth')) {
            function mb_strimwidth(string $string, int $start, int $width, string $trimmarker = '', ?string $encoding = null): string
            {
                $len = strlen($string);
                if ($start >= $len) {
                    return '';
                }
                $string = substr($string, $start);
                if (strlen($string) <= $width) {
                    return $string;
                }
                if ($width <= strlen($trimmarker)) {
                    return substr($trimmarker, 0, $width);
                }
                return substr($string, 0, $width - strlen($trimmarker)) . $trimmarker;
            }
        }

        // mb_internal_encoding (no-op)
        if (!function_exists('mb_internal_encoding')) {
            function mb_internal_encoding(?string $encoding = null): string|bool
            {
                return 'UTF-8';
            }
        }

        // mb_regex_encoding (no-op)
        if (!function_exists('mb_regex_encoding')) {
            function mb_regex_encoding(?string $encoding = null): string|bool
            {
                return 'UTF-8';
            }
        }

        // mb_http_output (no-op)
        if (!function_exists('mb_http_output')) {
            function mb_http_output(?string $encoding = null): string|bool
            {
                return 'UTF-8';
            }
        }

        // mb_http_input (no-op)
        if (!function_exists('mb_http_input')) {
            function mb_http_input(?string $type = null): string|array|bool
            {
                return 'UTF-8';
            }
        }

        // mb_detect_order (no-op)
        if (!function_exists('mb_detect_order')) {
            function mb_detect_order(array|string|null $encoding_list = null): array|bool
            {
                return ['UTF-8'];
            }
        }

        // mb_substitute_character (no-op)
        if (!function_exists('mb_substitute_character')) {
            function mb_substitute_character(string|int|null $substchar = null): string|int|bool
            {
                return '?';
            }
        }
    }
}
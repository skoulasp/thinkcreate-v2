<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Route;

class NoReservedTopLevelPath implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || trim($value) === '') {
            return;
        }

        $parsed = parse_url($value);

        if (($parsed['scheme'] ?? null) || ($parsed['host'] ?? null)) {
            return;
        }

        $path = $parsed['path'] ?? $value;
        $trimmedPath = trim($path, '/');

        if ($trimmedPath === '') {
            return;
        }

        $segments = explode('/', $trimmedPath);

        $segment = strtolower($segments[0]);

        if (in_array($segment, $this->reservedSegments(), true)) {
            $fail('This URL conflicts with a reserved top-level route.');
        }
    }

    /**
     * @return array<int, string>
     */
    private function reservedSegments(): array
    {
        $segments = [];

        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();

            if ($uri === '' || str_contains($uri, '{')) {
                continue;
            }

            $first = strtok($uri, '/');

            if ($first !== false && $first !== '') {
                $segments[] = strtolower($first);
            }
        }

        return array_values(array_unique($segments));
    }
}

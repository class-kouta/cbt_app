<?php

namespace App\Support;

use Illuminate\Http\Request;

final class Navigation
{
    /**
     * @param  array{route?: string, pattern?: string, except?: string|string[]}  $active
     */
    public static function isActive(array $active, ?Request $request = null): bool
    {
        $request ??= request();

        if (isset($active['route'])) {
            return $request->routeIs($active['route']);
        }

        if (! isset($active['pattern'])) {
            return false;
        }

        if (! $request->is($active['pattern'])) {
            return false;
        }

        if (! isset($active['except'])) {
            return true;
        }

        $except = $active['except'];

        if (is_array($except)) {
            foreach ($except as $pattern) {
                if ($request->is($pattern)) {
                    return false;
                }
            }

            return true;
        }

        return ! $request->is($except);
    }

    /**
     * @param  array<int, array{active?: array, children?: array<int, array{active?: array}>}>  $items
     */
    public static function sectionIsActive(array $items, ?Request $request = null): bool
    {
        foreach ($items as $item) {
            if (self::itemIsActive($item, $request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array{active?: array, children?: array<int, array{active?: array}>}  $item
     */
    public static function itemIsActive(array $item, ?Request $request = null): bool
    {
        if (isset($item['active']) && self::isActive($item['active'], $request)) {
            return true;
        }

        foreach ($item['children'] ?? [] as $child) {
            if (isset($child['active']) && self::isActive($child['active'], $request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{sections: array<string, bool>, items: array<string, bool>}
     */
    public static function initialOpenState(?Request $request = null): array
    {
        $request ??= request();
        $sections = [];
        $items = [];

        foreach (config('navigation.sections', []) as $section) {
            $sections[$section['id']] = self::sectionIsActive($section['items'], $request);

            foreach ($section['items'] as $item) {
                if (! isset($item['id'])) {
                    continue;
                }

                $items[$item['id']] = self::itemIsActive($item, $request);
            }
        }

        return [
            'sections' => $sections,
            'items' => $items,
        ];
    }
}

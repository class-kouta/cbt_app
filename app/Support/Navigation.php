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

    public static function selfWorkIsActive(?Request $request = null): bool
    {
        $request ??= request();

        if ($request->routeIs('home')) {
            return true;
        }

        foreach (config('navigation.sections', []) as $section) {
            if (self::sectionIsActive($section['items'], $request)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{selfWork: bool, sections: array<string, bool>, items: array<string, bool>}
     */
    public static function initialOpenState(?Request $request = null): array
    {
        $request ??= request();
        $sections = [];
        $items = [];
        $selfWorkOpen = false;

        foreach (config('navigation.sections', []) as $section) {
            $sectionActive = self::sectionIsActive($section['items'], $request);
            $sections[$section['id']] = $sectionActive;

            if ($sectionActive) {
                $selfWorkOpen = true;
            }

            foreach ($section['items'] as $item) {
                if (! isset($item['id'])) {
                    continue;
                }

                $items[$item['id']] = self::itemIsActive($item, $request);
            }
        }

        return [
            'selfWork' => $selfWorkOpen,
            'sections' => $sections,
            'items' => $items,
        ];
    }
}

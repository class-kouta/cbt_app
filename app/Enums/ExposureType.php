<?php

namespace App\Enums;

enum ExposureType: string
{
    case InVivo = 'in_vivo';
    case Imaginal = 'imaginal';
    case Interoceptive = 'interoceptive';

    public function label(): string
    {
        return match ($this) {
            self::InVivo => 'その場での暴露（in-vivo）',
            self::Imaginal => 'イメージ暴露（imaginal）',
            self::Interoceptive => '内受容暴露（interoceptive）',
        };
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function toFrontendArray(): array
    {
        return array_map(
            fn (self $type) => ['value' => $type->value, 'label' => $type->label()],
            self::cases()
        );
    }
}

<?php

namespace Takielias\Lab\Enums;

enum AlertType: string
{
    case info = 'info';
    case success = 'success';
    case warning = 'warning';
    case danger = 'danger';
    case validationError = 'validation-error';

    public static function values(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->getDisplayName();
        }
        return $result;
    }

    /**
     * Get the display-friendly name for the enum case.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        return Str::ucfirst(Str::snake($this->name, ' '));
    }
}

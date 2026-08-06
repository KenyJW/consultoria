<?php
declare(strict_types=1);

namespace App\Core;

final class Validator
{
    private array $errors = [];

    public function required(string $field, ?string $value, string $label): self
    {
        if (trim((string) $value) === '') {
            $this->errors[$field] = "{$label} es obligatorio.";
        }
        return $this;
    }

    public function email(string $field, ?string $value, string $label): self
    {
        if ($value !== null && trim($value) !== '' && ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "{$label} no tiene un formato valido.";
        }
        return $this;
    }

    public function in(string $field, string $value, array $allowed, string $label): self
    {
        if (! in_array($value, $allowed, true)) {
            $this->errors[$field] = "{$label} no es valido.";
        }
        return $this;
    }

    public function minLength(string $field, string $value, int $min, string $label): self
    {
        if (strlen($value) < $min) {
            $this->errors[$field] = "{$label} debe tener al menos {$min} caracteres.";
        }
        return $this;
    }

    public function errors(): array
    {
        return array_values($this->errors);
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }
}

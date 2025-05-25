<?php

namespace App\Models;

use Lib\Validations;
use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;

/**
 * @property int $id
 * @property string $phone
 * @property int $user_id
 */

class Client extends Model
{
    protected static string $table = 'clients';
    protected static array $columns = [
        'phone',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function validates(): void
    {
        if ($this->user_id == 0) {
            $this->addError('user_id', 'cannot be null');
            return;
        }

        $artist = Artist::all();

        foreach ($artist as $artist) {
            if ($artist->user_id === $this->user_id) {
                $this->addError('user_id', 'User already associated with a artist');
                return;
            }
        }
    }

    public function isValid(): bool
    {
        $this->validates();

        return empty($this->errors);
    }

    public function addError(string $attribute, string $message): void
    {
        $this->errors[] = "{$attribute} {$message}";
    }

    /**
     * @return string[] List of error messages, each as a string
     */

    public function getErrors(): array
    {
        return $this->errors;
    }
}

<?php

namespace App\Models;

use Lib\Validations;
use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;

/**
 * @property int $id
 * @property string $bio
 * @property string $portfolio_url
 * @property INT $ai_detection_count
 * @property int $user_id
 */

class Artist extends Model
{
    protected static string $table = 'artists';
    protected static array $columns = [
        'bio',
        'portfolio_url',
        'ai_detection_count',
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

        $clients = Client::all();

        foreach ($clients as $client) {
            if ($client->user_id === $this->user_id) {
                $this->addError('user_id', 'User already associated with a client');
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
        $this->errors[$attribute] = "{$attribute} {$message}";
    }

    /**
     * @return string[] List of error messages, each as a string
     */

    public function getErrors(): array
    {
        return $this->errors;
    }
}

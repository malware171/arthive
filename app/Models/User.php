<?php

namespace App\Models;

use Lib\Validations;
use Core\Database\ActiveRecord\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $encrypted_password
 * @property string $type
 * @property string $avatar_url
 */
class User extends Model
{
    protected static string $table = 'users';
    protected static array $columns = [
        'name',
        'email',
        'encrypted_password',
        'type',
        'avatar_url'
    ];

    protected array $errors = [];

    protected ?string $password = null;
    protected ?string $password_confirmation = null;

    public function isArtist(): bool
    {
        return Artist::findBy(['user_id' => $this->id]) !== null;
    }

    public function isClient(): bool
    {
        return Client::findBy(['user_id' => $this->id]) !== null;
    }

    public function artist(): ?Artist
    {
        return Artist::findBy(['user_id' => $this->id]);
    }

    public function client(): ?Client
    {
        return Client::findBy(['user_id' => $this->id]);
    }


    public function validates(): void
    {
        Validations::notEmpty('email', $this);
        Validations::notEmpty('password', $this);
        Validations::uniqueness('email', $this);
    }

    public function authenticate(string $password): bool
    {
        if ($this->encrypted_password == null) {
            return false;
        }

        return password_verify($password, $this->encrypted_password);
    }

    public static function findByEmail(string $email): User | null
    {
        return User::findBy(['email' => $email]);
    }

    public function addError(string $attribute, string $message): void
    {
        $this->errors[$attribute] = "{$attribute} {$message}";
    }

    /**
     * @return string[]
     */

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function __set(string $property, mixed $value): void
    {
        parent::__set($property, $value);

        if (
            $property === 'password' &&
            $this->newRecord() &&
            $value !== null && $value !== ''
        ) {
            $this->encrypted_password = password_hash($value, PASSWORD_DEFAULT);
        }
    }
}

<?php

namespace Squadron\User\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Squadron\Base\Models\Traits\HasUuid;
use Squadron\User\Exceptions\SquadronUserException;

/**
 * Class User.
 *
 * @property string $uuid
 * @property int    $active
 * @property string $email
 * @property string $firstName
 * @property string $lastName
 * @property string $password
 * @property string $rememberToken
 * @property string $role
 * @property int    $createdAt
 * @property int    $updatedAt
 */
class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasUuid;

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';

    protected $table = 'user';
    protected $rememberTokenName = 'rememberToken';
    protected $primaryKey = 'uuid';
    protected $keyType = 'uuid';
    public $incrementing = false;

    protected $fillable = ['firstName', 'lastName'];
    protected $hidden = ['password', 'rememberToken'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function setPasswordAttribute($password): void
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function setRole($role): bool
    {
        /** @var static|null $currentUser */
        $currentUser = Auth::user();

        if ($currentUser !== null)
        {
            $rolesHierarchy = config('squadron.user.rolesHierarchy', []);
            $rolesHierarchy = is_array($rolesHierarchy[$currentUser->role]) ? $rolesHierarchy[$currentUser->role] : [];

            if ($currentUser->isRoot() || in_array($role, $rolesHierarchy, true))
            {
                $this->role = $role;

                return true;
            }
        }

        return false;
    }

    public function isCurrent(): bool
    {
        /** @var static|null $currentUser */
        $currentUser = Auth::user();

        return $currentUser !== null ? $currentUser->getKey() === $this->getKey() : false;
    }

    public function isRoot(): bool
    {
        return $this->role === 'root';
    }

    public function isBaseUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get socials associated with record.
     */
    public function socials(): HasMany
    {
        return $this->hasMany(config('squadron.user.models.userSocial'), 'userUuid');
    }

    public function validateForPassportPasswordGrant($password): bool
    {
        if (Hash::check($password, $this->getAuthPassword()))
        {
            if (! $this->active)
            {
                throw SquadronUserException::userDeactivated();
            }

            return true;
        }

        return false;
    }
}

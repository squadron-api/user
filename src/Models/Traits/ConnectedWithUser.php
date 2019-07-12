<?php

namespace Squadron\User\Models\Traits;

use Illuminate\Support\Facades\Auth;

use Squadron\User\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait ConnectedWithUser.
 *
 * @property $userUuid
 * @property User $user
 *
 * @method static Builder ofCurrentUser()
 * @method static Builder ofUser(string $userUuid)
 */
trait ConnectedWithUser
{
    /**
     * Get the user associated with record.
     */
    public function user()
    {
        return $this->belongsTo(config('squadron.user.models.user'), 'userUuid');
    }

    /**
     * Scope a query to only include current user's models.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeOfCurrentUser(Builder $query): Builder
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        return $query->where('userUuid', $currentUser->getKey());
    }

    /**
     * Scope a query to only include specific user's models.
     *
     * @param Builder $query
     * @param string  $userUuid
     *
     * @return Builder
     */
    public function scopeOfUser(Builder $query, string $userUuid): Builder
    {
        return $query->where('userUuid', $userUuid);
    }
}

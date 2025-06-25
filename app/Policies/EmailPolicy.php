<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Email;

class EmailPolicy
{
    /**
     * Determine if the user can view the email.
     */
    public function view(User $user, Email $email): bool
    {
        return $user->id === $email->user_id;
    }

    /**
     * Determine if the user can update the email.
     */
    public function update(User $user, Email $email): bool
    {
        return $user->id === $email->user_id;
    }

    /**
     * Determine if the user can delete the email.
     */
    public function delete(User $user, Email $email): bool
    {
        return $user->id === $email->user_id;
    }
}
<?php

namespace App\Auth;

use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class CorreoTokenRepository extends DatabaseTokenRepository
{
    /**
     * Build the record payload for the table using 'correo' instead of 'email'.
     *
     * @param  string  $email
     * @param  string  $token
     * @return array
     */
    protected function getPayload($email, $token)
    {
        return ['correo' => $email, 'token' => $this->hasher->make($token), 'created_at' => new \Illuminate\Support\Carbon];
    }

    /**
     * Determine if a token record exists and is valid, using 'correo' instead of 'email'.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $token
     * @return bool
     */
    public function exists(CanResetPasswordContract $user, $token)
    {
        $record = (array) $this->getTable()->where(
            'correo', $user->getEmailForPasswordReset()
        )->first();

        return $record &&
               ! $this->tokenExpired($record['created_at']) &&
                 $this->hasher->check($token, $record['token']);
    }

    /**
     * Determine if the given user recently created a password reset token, using 'correo'.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return bool
     */
    public function recentlyCreatedToken(CanResetPasswordContract $user)
    {
        $record = (array) $this->getTable()->where(
            'correo', $user->getEmailForPasswordReset()
        )->first();

        return $record && $this->tokenRecentlyCreated($record['created_at']);
    }

    /**
     * Delete all existing reset tokens from the database for the given user.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return int
     */
    protected function deleteExisting(CanResetPasswordContract $user)
    {
        return $this->getTable()->where('correo', $user->getEmailForPasswordReset())->delete();
    }
}

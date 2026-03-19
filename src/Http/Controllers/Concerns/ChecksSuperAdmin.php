<?php

namespace SimpleChat\Http\Controllers\Concerns;

trait ChecksSuperAdmin
{
    /**
     * Returns true if the currently authenticated user holds the super-admin
     * permission configured under simple-chat.support.permissions.super_admin.
     */
    protected function isSuperAdmin(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $perm = config('simple-chat.support.permissions.super_admin', 'simple-chat-super-admin');

        return auth()->user()->can($perm);
    }
}

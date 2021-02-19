<?php

namespace App\Http\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Session;

trait Impersonate
{
    /**
     * Impersonate a User received in request
     *
     * @param  User  $user
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function impersonateUser(User $user)
    {
        // Guard against administrator impersonate
        if (!$user->can('is-admin')) {
            Session::flash('error', 'Impersonate disabled for this user.');
            return redirect()->to('/');
        }
        auth()->user()->setImpersonating($user->id);
        return redirect()->to('/');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function stopImpersonate()
    {
        auth()->user()->stopImpersonating();
        //Session::flash('success', 'Welcome back!');
        return redirect()->to('/');
    }
}

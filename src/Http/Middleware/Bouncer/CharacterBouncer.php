<?php

/*
 * This file is part of SeAT
 *
 * Copyright (C) 2015, 2016, 2017  Leon Jacobs
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace Seat\Web\Http\Middleware\Bouncer;

use Closure;
use Illuminate\Http\Request;
use Seat\Web\Exceptions\BouncerException;

/**
 * Class CharacterBouncer.
 * @package Seat\Web\Http\Middleware
 */
class CharacterBouncer
{
    /**
     * Handle an incoming request.
     *
     * This filter checks if a specific permission exists as
     * well as ensures that an affiliation to a character
     * exists.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string                   $permission
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Seat\Web\Exceptions\BouncerException
     */
    public function handle(Request $request, Closure $next, string $permission = null)
    {

        // Get the currently logged in user
        $user = auth()->user();

        if (! $request->character_id)
            throw new BouncerException(
                'CharacterBouncer was unable to determine a character_id');

        // Set the request charID
        $user->setCharacterId($request->character_id);

        // Check on the clipboard if this permission
        // should be granted.
        if ($user->has('character.' . $permission))
            return $next($request);

        $message = 'Request to ' . $request->path() . ' was ' .
            'denied by the characterbouncer. The permission required is ' .
            'character.' . $permission . '.';

        event('security.log', [$message, 'authorization']);

        // Redirect away from the original request
        return redirect()->route('auth.unauthorized');

    }
}

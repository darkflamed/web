<?php

/*
 * This file is part of SeAT
 *
 * Copyright (C) 2015, 2016, 2017, 2018, 2019  Leon Jacobs
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

namespace Seat\Web\Http\Controllers\Character;

use Seat\Eveapi\Models\RefreshToken;
use Seat\Web\Http\Controllers\Controller;
use Seat\Web\Http\DataTables\Character\Financial\MarketDataTable;
use Seat\Web\Http\DataTables\Scopes\CharacterScope;
use Seat\Web\Http\DataTables\Scopes\Filters\MarketOrderTypeScope;
use Seat\Web\Http\DataTables\Scopes\Filters\MarketStatusScope;
use Seat\Web\Models\User;

/**
 * Class MarketController.
 * @package Seat\Web\Http\Controllers\Character
 */
class MarketController extends Controller
{
    /**
     * @param int $character_id
     * @param \Seat\Web\Http\DataTables\Character\Financial\MarketDataTable $dataTable
     * @return mixed
     */
    public function index(int $character_id, MarketDataTable $dataTable)
    {
        $token = RefreshToken::where('character_id', $character_id)->first();
        if ($token) {
            $characters = User::with('characters')->find($token->user_id)->characters;
        } else {
            $characters = collect();
        }

        return $dataTable
            ->addScope(new CharacterScope('character.market', $character_id, request()->input('characters', [])))
            ->addScope(new MarketStatusScope(request()->input('filters.status')))
            ->addScope(new MarketOrderTypeScope(request()->input('filters.type')))
            ->render('web::character.market', compact('characters'));
    }
}

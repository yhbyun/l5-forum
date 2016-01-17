<?php

namespace App;

use Cache;
use DB;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    public static function relationArrayWithCache()
    {
        return Cache::remember('all_role_users', $minutes = 60, function () {
            return DB::table('role_user')->get();
        });
    }

    public static function rolesArrayWithCache()
    {
        return Cache::remember('all_roles', $minutes = 60, function () {
            return DB::table('roles')->get();
        });
    }
}

<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;

trait PersonalClient
{

    protected function createPersonalClient()
    {
        Passport::$hashesClientSecrets = false;

        $this->artisan(
            'passport:client',
            ['--name' => config('app.name'), '--personal' => null]
        );

        // use the query builder instead of the model, to retrieve the client secret
        return DB::table('oauth_clients')
            ->where('personal_access_client','=',true)
            ->first();
    }


}

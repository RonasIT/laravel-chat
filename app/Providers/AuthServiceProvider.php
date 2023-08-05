<?php

namespace App\Providers;

use App\Models\Berth;
use App\Models\BerthAmenity;
use App\Models\Boat;
use App\Models\BoatType;
use App\Models\Booking;
use App\Models\Country;
use App\Models\Media;
use App\Models\Setting;
use App\Models\User;
use App\Models\VerificationCode;
use App\Policies\BerthAmenityPolicy;
use App\Policies\BerthPolicy;
use App\Policies\BoatPolicy;
use App\Policies\BoatTypePolicy;
use App\Policies\BookingPolicy;
use App\Policies\CountryPolicy;
use App\Policies\MediaPolicy;
use App\Policies\SettingPolicy;
use App\Policies\UserPolicy;
use App\Policies\VerificationCodePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}

<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use App\Services\LgiPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse
        {
            public function toResponse($request)
            {
                return redirect('/');
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::authenticateUsing(function (Request $request) {
            Validator::make($request->except('_token'), [
                'nik' => ['required', 'string', 'max:10'],
                'password' => ['required', 'string', Password::default()],
            ])->validate();

            $nik = $request->nik;
            $password = $request->password;

            
            $user = User::query()->where('NIK', $nik);
            if (! $user->first()) {
                throw ValidationException::withMessages([
                    'nik' => ['User not Found'],
                ]);
            }

            $encryptedPassword = LgiPassword::Encrypt($password);

            if ($user->first()->Password !== $encryptedPassword) {
                throw ValidationException::withMessages([
                    'password' => ['Invalid password.'],
                ]);
            }

            //! Validate Access Level
            $user = $user->whereHas('UserGroup', function($query1){
                $query1->whereHas('Group', function($query2){
                    $query2->where('AppCode', 'lgi-booking');
                    $query2->whereHas('App', function($query3){
                        $query3->where('AppCode', 'lgi-booking');
                    });
                });
            })
            ->with('UserGroup')
            ->with('UserGroup.Group')
            ->with('UserGroup.Group.App')
            ->first();

            /* THROW ERROR IF DOESN'T HAVE ACCESS */
            if( !$user ){
                throw ValidationException::withMessages([
                    "error" => "doesn't have access.",
                ]);
            }

            return $user;
        });

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}

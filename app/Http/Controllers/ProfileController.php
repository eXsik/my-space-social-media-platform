<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),<?php

            namespace App\Models;
            
            // use Illuminate\Contracts\Auth\MustVerifyEmail;
            
            use Illuminate\Contracts\Auth\MustVerifyEmail;
            use Laravel\Sanctum\HasApiTokens;
            use Spatie\Sluggable\SlugOptions;
            use Illuminate\Notifications\Notifiable;
            use Illuminate\Database\Eloquent\Factories\HasFactory;
            use Illuminate\Foundation\Auth\User as Authenticatable;
            use Spatie\Sluggable\HasSlug;
            
            class User extends Authenticatable implements MustVerifyEmail
            {
                use HasApiTokens, HasFactory, Notifiable, HasSlug;
            
                /**
                 * The attributes that are mass assignable.
                 *
                 * @var array<int, string>
                 */
                protected $fillable = [
                    'name',
                    'username',
                    'email',
                    'password',
                ];
            
                /**
                 * The attributes that should be hidden for serialization.
                 *
                 * @var array<int, string>
                 */
                protected $hidden = [
                    'password',
                    'remember_token',
                ];
            
                /**
                 * The attributes that should be cast.
                 *
                 * @var array<string, string>
                 */
                protected $casts = [
                    'email_verified_at' => 'datetime',
                    'password' => 'hashed',
                ];
            
                public function getSlugOptions() : SlugOptions
            {
                return SlugOptions::create()
                    ->generateSlugsFrom('name')
                    ->saveSlugsTo('username')
                    ->doNotGenerateSlugsOnUpdate();
            }
            }
            
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

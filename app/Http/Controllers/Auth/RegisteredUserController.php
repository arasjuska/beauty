<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $rolesWithLabels = [
            'specialist' => __('Specialist'),
            'salon super admin' => __('Salon'),
            'chain super admin' => __('Chain salon'),
        ];

        return view('auth.register', [
            'roles' => array_keys($rolesWithLabels),
            'roleLabels' => $rolesWithLabels,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'register_as' => ['nullable', 'boolean'],
                'role' => ['required_if:register_as,1'],
            ],
            [
                'role.required_if' => __('Please select a role (specialist, salon, or chain salon) if you are registering as a specialist or company.'),
            ]
        );

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $roleName = $validatedData['role'] ?? 'user';
        if ($role = Role::where('name', $roleName)->first()) {
            $user->roles()->attach($role);
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}

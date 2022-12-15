<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Axlon\PostalCodeValidation\Rules\PostalCode;
// use Axlon\PostalCodeValidation\Extensions\PostalCode;
// use Axlon\PostalCodeValidation\Extensions\PostalCode;
// use Axlon\PostalCodeValidation\Extensions\PostalCode;
// use Axlon\PostalCodeValidation\Extensions\PostalCode;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'postal_code' => 'required|regex:/^[0-9]{3,7}$/',
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'postal_code' => $input['postal_code'],
            'address' => $input['address'],
            'password' => Hash::make($input['password']),
        ]);
    }
}

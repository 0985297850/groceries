<?php

namespace Database\Factories;

use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    public function definition()
    {
        return [
            'address' => 'Ha Noi',
            'first_name' => "Admin",
            'last_name' => "System",
            'gender' => "other",
            'phone' => '0985297855'
        ];
    }
}

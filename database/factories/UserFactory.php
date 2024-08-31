<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'email' => 'admin@gmail.com',
            'password' => bcrypt('Admin@123456'), // Mật khẩu mặc định
            'role' => 'admin',
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            // Tạo user profile sau khi tạo user
            $user->profile()->create([
                'address' => 'Ha Noi',
                'first_name' => "Admin",
                'last_name' => "System",
                'gender' => "other",
                'phone' => '0985297855'
            ]);
        });
    }
}

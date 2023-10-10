<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $data = [
      [
        'name' => 'Lennyn',
        'username' => 'lennynleyva',
        'email' => 'lennyn@hotmail.com',
        'password' => bcrypt('admin123'),
        'active' => true,
        'email_verified_at' => now(),
      ],
    ];

    foreach ($data as $user) {
      User::updateOrCreate(['username' => $user['username']], $user)->assignRole('root');
    }
  }
}

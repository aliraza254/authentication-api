<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();


//        $this->call(RoleSeeder::class);

//        Role::create(['name' => 'admin']);
//        Role::create(['name' => 'writer']);
//        Role::create(['name' => 'user']);

        // create permissions
        Permission::create(['name' => 'view', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'add', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'edit', 'guard_name' => 'sanctum']);
        Permission::create(['name' => 'delete', 'guard_name' => 'sanctum']);

        Role::create(['name' => 'super-admin', 'guard_name' => 'sanctum'])
            ->givePermissionTo(Permission::all());

        Role::create(['name' => 'admin', 'guard_name' => 'sanctum'])
            ->givePermissionTo('add');

        Role::create(['name' => 'user', 'guard_name' => 'sanctum'])
            ->givePermissionTo('view');

        \App\Models\User::factory()->create([
            'name' => 'ali',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin12345'),
            'guard_name' => 'sanctum',
        ])->assignRole('super-admin');
    }
}

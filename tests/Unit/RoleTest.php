<?php

namespace Tests\Unit;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_valid_roles()
    {
        $validRoles = ['admin', 'supervisor'];

        foreach ($validRoles as $roleName) {
            $role = Role::create(['name' => $roleName]);
            $this->assertDatabaseHas('roles', ['name' => $roleName]);
        }
    }

    public function test_cannot_create_invalid_roles()
    {
        $invalidRoleName = 'user';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid role name: {$invalidRoleName}");

        Role::create(['name' => $invalidRoleName]);

        $this->assertDatabaseMissing('roles', ['name' => $invalidRoleName]);
    }
}

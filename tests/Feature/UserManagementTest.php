<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superuser;

    protected User $owner;

    protected User $warehouse;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superuser = User::where('email', 'super@omahvisual.com')->firstOrFail();
        $this->owner = User::where('email', 'owner@omahvisual.com')->firstOrFail();
        $this->warehouse = User::where('email', 'warehouse@omahvisual.com')->firstOrFail();
    }

    public function test_guest_is_redirected_to_login_from_user_management(): void
    {
        $response = $this->get(route('users.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_is_forbidden_from_user_management(): void
    {
        $response = $this->actingAs($this->warehouse)->get(route('users.index'));
        $response->assertStatus(403);
    }

    public function test_superuser_and_owner_can_view_users_index(): void
    {
        $responseSuper = $this->actingAs($this->superuser)->get(route('users.index', ['search' => 'super@omahvisual.com']));
        $responseSuper->assertStatus(200);
        $responseSuper->assertSee('Manajemen Pengguna');
        $responseSuper->assertSee('super@omahvisual.com');
        $responseSuper->assertSee('Teknisi Servis');

        $responseOwner = $this->actingAs($this->owner)->get(route('users.index'));
        $responseOwner->assertStatus(200);
    }

    public function test_superuser_can_create_new_user_with_teknisi_servis_role(): void
    {
        $email = 'test.teknisi.'.uniqid().'@omahvisual.com';

        $response = $this->actingAs($this->superuser)->post(route('users.store'), [
            'name' => 'Doni Teknisi Baru',
            'email' => $email,
            'phone' => '089988776655',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Teknisi Servis',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $newUser = User::where('email', $email)->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('Doni Teknisi Baru', $newUser->name);
        $this->assertTrue($newUser->hasRole('Teknisi Servis'));
        $this->assertTrue(Hash::check('password123', $newUser->password));

        // Verify Audit Log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'create_user',
            'entity_type' => 'User',
            'entity_id' => $newUser->id,
        ]);
    }

    public function test_superuser_can_update_user_and_change_role(): void
    {
        $user = User::factory()->create([
            'name' => 'User Before Update',
            'email' => 'update.me.'.uniqid().'@omahvisual.com',
        ]);
        $user->assignRole('Warehouse');

        $response = $this->actingAs($this->superuser)->put(route('users.update', $user), [
            'name' => 'User After Update',
            'email' => $user->email,
            'phone' => '08111222333',
            'role' => 'Teknisi Servis',
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('User After Update', $user->name);
        $this->assertEquals('08111222333', $user->phone);
        $this->assertTrue($user->hasRole('Teknisi Servis'));
        $this->assertFalse($user->hasRole('Warehouse'));
    }

    public function test_superuser_can_reset_user_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldSecretPassword123'),
        ]);

        $response = $this->actingAs($this->superuser)->post(route('users.reset-password', $user));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_user_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->superuser)->delete(route('users.destroy', $this->superuser));
        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $this->superuser->id]);
    }

    public function test_superuser_can_delete_other_user(): void
    {
        $userToDelete = User::factory()->create();
        $userToDelete->assignRole('Warehouse');

        $response = $this->actingAs($this->superuser)->delete(route('users.destroy', $userToDelete));
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    public function test_superuser_can_create_user_with_multiple_roles(): void
    {
        $email = 'multi.role.'.uniqid().'@omahvisual.com';

        $response = $this->actingAs($this->superuser)->post(route('users.store'), [
            'name' => 'Agus Warehouse & Teknisi',
            'email' => $email,
            'phone' => '081299887766',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => ['Warehouse', 'Teknisi Servis'],
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $newUser = User::where('email', $email)->first();
        $this->assertNotNull($newUser);
        $this->assertTrue($newUser->hasRole('Warehouse'));
        $this->assertTrue($newUser->hasRole('Teknisi Servis'));
        $this->assertEquals(2, $newUser->roles()->count());
    }

    public function test_superuser_can_update_user_to_multiple_roles(): void
    {
        $user = User::factory()->create([
            'email' => 'single.to.multi.'.uniqid().'@omahvisual.com',
        ]);
        $user->assignRole('Warehouse');

        $response = $this->actingAs($this->superuser)->put(route('users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => ['Warehouse', 'Teknisi Servis', 'Admin LED'],
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue($user->hasRole('Warehouse'));
        $this->assertTrue($user->hasRole('Teknisi Servis'));
        $this->assertTrue($user->hasRole('Admin LED'));
        $this->assertEquals(3, $user->roles()->count());
    }

    public function test_cannot_remove_superuser_role_if_only_one_superuser(): void
    {
        // Ensure there is only 1 superuser
        $otherSuperusers = User::role('Superuser')->where('id', '!=', $this->superuser->id)->get();
        foreach ($otherSuperusers as $other) {
            $other->removeRole('Superuser');
        }

        $response = $this->actingAs($this->superuser)->put(route('users.update', $this->superuser), [
            'name' => $this->superuser->name,
            'email' => $this->superuser->email,
            'roles' => ['Teknisi Servis'],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->superuser->refresh();
        $this->assertTrue($this->superuser->hasRole('Superuser'));
    }
}

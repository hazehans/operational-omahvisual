<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileAndAvatarTest extends TestCase
{
    protected User $warehouse;

    protected function setUp(): void
    {
        parent::setUp();
        $this->warehouse = User::where('email', 'warehouse@omahvisual.com')->firstOrFail();
    }

    public function test_guest_is_redirected_from_profile_page(): void
    {
        $response = $this->get(route('profile.edit'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->warehouse)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Profil & Keamanan Akun');
        $response->assertSee($this->warehouse->name);
        $response->assertSee($this->warehouse->email);
        $response->assertSee('Warehouse');
    }

    public function test_user_can_update_profile_info_and_upload_avatar(): void
    {
        Storage::fake('public');

        $avatar = UploadedFile::fake()->image('avatar.jpg', 400, 400);

        $response = $this->actingAs($this->warehouse)->put(route('profile.update'), [
            'name' => 'Budi Gudang Omah Visual',
            'phone' => '081234567890',
            'avatar' => $avatar,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->warehouse->refresh();
        $this->assertEquals('Budi Gudang Omah Visual', $this->warehouse->name);
        $this->assertEquals('081234567890', $this->warehouse->phone);
        $this->assertNotNull($this->warehouse->avatar_path);
        Storage::disk('public')->assertExists($this->warehouse->avatar_path);
    }

    public function test_user_can_remove_avatar(): void
    {
        Storage::fake('public');

        $avatar = UploadedFile::fake()->image('my-avatar.png');
        $path = $avatar->store('avatars', 'public');
        $this->warehouse->update(['avatar_path' => $path]);

        $response = $this->actingAs($this->warehouse)->put(route('profile.update'), [
            'name' => 'Budi Gudang Omah Visual',
            'phone' => '081234567890',
            'remove_avatar' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->warehouse->refresh();
        $this->assertNull($this->warehouse->avatar_path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_user_can_update_password_with_valid_current_password(): void
    {
        // Set known password
        $this->warehouse->update(['password' => Hash::make('oldpassword123')]);

        $response = $this->actingAs($this->warehouse)->put(route('profile.password'), [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->warehouse->refresh();
        $this->assertTrue(Hash::check('newpassword456', $this->warehouse->password));

        // Restore default password for demo continuity
        $this->warehouse->update(['password' => Hash::make('password123')]);
    }

    public function test_user_cannot_update_password_with_wrong_current_password(): void
    {
        $this->warehouse->update(['password' => Hash::make('password123')]);

        $response = $this->actingAs($this->warehouse)->put(route('profile.password'), [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->warehouse->refresh();
        $this->assertTrue(Hash::check('password123', $this->warehouse->password));
    }
}

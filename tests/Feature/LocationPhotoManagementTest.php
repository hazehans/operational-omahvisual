<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LocationPhotoManagementTest extends TestCase
{
    protected User $warehouse;

    protected User $adminLed;

    protected function setUp(): void
    {
        parent::setUp();
        $this->warehouse = User::where('email', 'warehouse@omahvisual.com')->firstOrFail();
        $this->adminLed = User::where('email', 'admin@omahvisual.com')->firstOrFail();
    }

    public function test_user_can_view_locations_index_page(): void
    {
        $response = $this->actingAs($this->warehouse)->get(route('locations.index'));

        $response->assertStatus(200);
        $response->assertSee('Titik Lokasi & Lemari Gudang');
        $response->assertSee('GU-ALM-1');
    }

    public function test_authorized_user_can_create_location_with_photo(): void
    {
        Storage::fake('public');
        Location::where('code', 'GU-ALM-4')->delete();

        $image = UploadedFile::fake()->image('lemari-baru.jpg', 800, 600);

        $response = $this->actingAs($this->warehouse)->post(route('locations.store'), [
            'name' => 'Lemari 4 (Kabel Power & Audio)',
            'code' => 'GU-ALM-4',
            'type' => 'cabinet',
            'description' => 'Lemari khusus kabel power distro dan snake audio',
            'image' => $image,
            'is_active' => true,
        ]);

        $location = Location::where('code', 'GU-ALM-4')->first();
        $this->assertNotNull($location);

        $response->assertRedirect(route('locations.show', $location));

        $this->assertNotNull($location->image_path);
        Storage::disk('public')->assertExists($location->image_path);

        $location->delete();
    }

    public function test_user_can_view_location_detail_with_photo(): void
    {
        $location = Location::where('code', 'GU-ALM-1')->firstOrFail();

        $response = $this->actingAs($this->warehouse)->get(route('locations.show', $location));

        $response->assertStatus(200);
        $response->assertSee($location->name);
        $response->assertSee($location->code);
    }

    public function test_authorized_user_can_update_location_and_replace_photo(): void
    {
        Storage::fake('public');

        $oldImage = UploadedFile::fake()->image('old-location.jpg');
        $oldPath = $oldImage->store('locations', 'public');

        $location = Location::create([
            'name' => 'Rak Display Tester',
            'code' => 'GU-RAK-TEST',
            'type' => 'shelf',
            'description' => 'Rak uji modul dan receiving card',
            'image_path' => $oldPath,
            'is_active' => true,
        ]);

        $newImage = UploadedFile::fake()->image('new-location.png');

        $response = $this->actingAs($this->warehouse)->put(route('locations.update', $location), [
            'name' => 'Rak Display Tester Diperbarui',
            'code' => 'GU-RAK-TEST',
            'type' => 'shelf',
            'description' => 'Rak uji modul terbaru dengan lampu kerja',
            'image' => $newImage,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('locations.show', $location));

        $location->refresh();
        $this->assertEquals('Rak Display Tester Diperbarui', $location->name);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($location->image_path);

        // Cleanup
        $location->delete();
    }

    public function test_user_can_remove_location_photo(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('temp-location.jpg');
        $path = $image->store('locations', 'public');

        $location = Location::create([
            'name' => 'Locker Khusus Baut',
            'code' => 'GU-LCK-BAUT',
            'type' => 'bin',
            'description' => 'Penyimpanan mur baut rigging',
            'image_path' => $path,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->warehouse)->put(route('locations.update', $location), [
            'name' => 'Locker Khusus Baut',
            'code' => 'GU-LCK-BAUT',
            'type' => 'bin',
            'description' => 'Penyimpanan mur baut rigging',
            'remove_image' => true,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('locations.show', $location));

        $location->refresh();
        $this->assertNull($location->image_path);
        Storage::disk('public')->assertMissing($path);

        // Cleanup
        $location->delete();
    }

    public function test_unauthorized_user_cannot_create_location(): void
    {
        $response = $this->actingAs($this->adminLed)->post(route('locations.store'), [
            'name' => 'Lokasi Terlarang',
            'code' => 'GU-NO-ACCESS',
            'type' => 'room',
        ]);

        $response->assertStatus(403);
    }
}

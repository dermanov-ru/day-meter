<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DayEntry;
use App\Models\DayPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PhotoTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private DayEntry $dayEntry;
    private string $testDate;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->testDate = '2026-01-06';
        $this->dayEntry = DayEntry::create([
            'user_id' => $this->user->id,
            'date' => $this->testDate,
        ]);
    }

    public function test_can_upload_photo()
    {
        $this->actingAs($this->user);
        
        $file = UploadedFile::fake()->image('photo.jpg', 800, 600);
        
        $response = $this->post(route('photos.upload'), [
            'file' => $file,
            'date' => $this->testDate,
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'photo' => ['id', 'url', 'thumbnail_url', 'comment']
        ]);

        $this->assertDatabaseHas('day_photos', [
            'user_id' => $this->user->id,
            'day_entry_id' => $this->dayEntry->id,
        ]);
    }

    public function test_can_delete_photo()
    {
        $this->actingAs($this->user);

        $photo = DayPhoto::create([
            'user_id' => $this->user->id,
            'day_entry_id' => $this->dayEntry->id,
            'file_path' => 'photos/1/2026-01-06/test.jpg',
        ]);

        $response = $this->delete(route('photos.delete', $photo));

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('day_photos', [
            'id' => $photo->id,
        ]);
    }

    public function test_can_update_photo_comment()
    {
        $this->actingAs($this->user);

        $photo = DayPhoto::create([
            'user_id' => $this->user->id,
            'day_entry_id' => $this->dayEntry->id,
            'file_path' => 'photos/1/2026-01-06/test.jpg',
        ]);

        $newComment = 'This is a test comment';
        $response = $this->patch(route('photos.updateComment', $photo), [
            'comment' => $newComment,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('day_photos', [
            'id' => $photo->id,
            'comment' => $newComment,
        ]);
    }

    public function test_cannot_delete_other_users_photo()
    {
        $otherUser = User::factory()->create();
        
        $photo = DayPhoto::create([
            'user_id' => $otherUser->id,
            'day_entry_id' => $this->dayEntry->id,
            'file_path' => 'photos/1/2026-01-06/test.jpg',
        ]);

        $this->actingAs($this->user);
        $response = $this->delete(route('photos.delete', $photo));

        $response->assertForbidden();
    }

    public function test_cannot_update_other_users_photo_comment()
    {
        $otherUser = User::factory()->create();
        
        $photo = DayPhoto::create([
            'user_id' => $otherUser->id,
            'day_entry_id' => $this->dayEntry->id,
            'file_path' => 'photos/1/2026-01-06/test.jpg',
        ]);

        $this->actingAs($this->user);
        $response = $this->patch(route('photos.updateComment', $photo), [
            'comment' => 'Hacked!',
        ]);

        $response->assertForbidden();
    }
}

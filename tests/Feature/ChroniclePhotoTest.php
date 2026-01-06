<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DayEntry;
use App\Models\DayPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChroniclePhotoTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $testMonth;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->testMonth = '2026-01';
    }

    public function test_chronicle_displays_photos_with_comments()
    {
        // Create a day entry
        $dayEntry = DayEntry::create([
            'user_id' => $this->user->id,
            'date' => '2026-01-06',
        ]);

        // Create photos with comments
        $photo1 = DayPhoto::create([
            'user_id' => $this->user->id,
            'day_entry_id' => $dayEntry->id,
            'file_path' => 'photos/1/2026-01-06/test1.jpg',
            'comment' => 'First photo comment',
            'sort_order' => 1,
        ]);

        $photo2 = DayPhoto::create([
            'user_id' => $this->user->id,
            'day_entry_id' => $dayEntry->id,
            'file_path' => 'photos/1/2026-01-06/test2.jpg',
            'comment' => 'Second photo comment',
            'sort_order' => 2,
        ]);

        // Visit chronicle page
        $response = $this->actingAs($this->user)->get(route('chronicle.index', ['month' => $this->testMonth]));

        $response->assertOk();
        // Check that photos section is displayed
        $response->assertSeeText('📷 Фото дня');
        $response->assertSeeText('First photo comment');
        $response->assertSeeText('Second photo comment');
    }

    public function test_chronicle_displays_photos_without_comments()
    {
        $dayEntry = DayEntry::create([
            'user_id' => $this->user->id,
            'date' => '2026-01-06',
        ]);

        // Create photo without comment
        DayPhoto::create([
            'user_id' => $this->user->id,
            'day_entry_id' => $dayEntry->id,
            'file_path' => 'photos/1/2026-01-06/test.jpg',
            'comment' => null,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->user)->get(route('chronicle.index', ['month' => $this->testMonth]));

        $response->assertOk();
        // Check that photo section is displayed even without comments
        $response->assertSeeText('📷 Фото дня');
    }

    public function test_chronicle_does_not_show_photos_for_other_users()
    {
        $otherUser = User::factory()->create();
        
        $dayEntry = DayEntry::create([
            'user_id' => $otherUser->id,
            'date' => '2026-01-06',
        ]);

        DayPhoto::create([
            'user_id' => $otherUser->id,
            'day_entry_id' => $dayEntry->id,
            'file_path' => 'photos/1/2026-01-06/test.jpg',
            'comment' => 'Secret photo',
        ]);

        // Logged in user should not see other user's photos
        $response = $this->actingAs($this->user)->get(route('chronicle.index', ['month' => $this->testMonth]));

        $response->assertOk();
        $response->assertDontSeeText('Secret photo');
    }
}

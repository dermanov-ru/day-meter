<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DayEntry;
use App\Models\DayPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhotoChronicleTest extends TestCase
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

    public function test_photo_chronicle_page_loads()
    {
        $response = $this->actingAs($this->user)->get(route('photos.chronicle.index'));

        $response->assertOk();
        $response->assertSeeText('Фото-хроника');
    }

    public function test_photo_chronicle_shows_only_days_with_photos()
    {
        // Create days with and without photos
        $dayWithPhotos = DayEntry::create([
            'user_id' => $this->user->id,
            'date' => '2026-01-06',
        ]);

        $dayWithoutPhotos = DayEntry::create([
            'user_id' => $this->user->id,
            'date' => '2026-01-05',
        ]);

        // Add photo to first day
        DayPhoto::create([
            'user_id' => $this->user->id,
            'day_entry_id' => $dayWithPhotos->id,
            'file_path' => 'photos/1/2026-01-06/test.jpg',
            'comment' => 'Test comment',
        ]);

        $response = $this->actingAs($this->user)->get(route('photos.chronicle.index', ['month' => $this->testMonth]));

        $response->assertOk();
        $response->assertSeeText('06.01.2026'); // formatted date
        $response->assertSeeText('Test comment');
        $response->assertDontSeeText('05.01.2026'); // other date should not appear
    }

    public function test_photo_chronicle_groups_photos_by_date()
    {
        $dayEntry = DayEntry::create([
            'user_id' => $this->user->id,
            'date' => '2026-01-06',
        ]);

        // Create multiple photos for same day
        DayPhoto::create([
            'user_id' => $this->user->id,
            'day_entry_id' => $dayEntry->id,
            'file_path' => 'photos/1/2026-01-06/test1.jpg',
            'comment' => 'First photo',
            'sort_order' => 1,
        ]);

        DayPhoto::create([
            'user_id' => $this->user->id,
            'day_entry_id' => $dayEntry->id,
            'file_path' => 'photos/1/2026-01-06/test2.jpg',
            'comment' => 'Second photo',
            'sort_order' => 2,
        ]);

        $response = $this->actingAs($this->user)->get(route('photos.chronicle.index', ['month' => $this->testMonth]));

        $response->assertOk();
        $response->assertSeeText('First photo');
        $response->assertSeeText('Second photo');
        $response->assertSeeText('2 фото');
    }

    public function test_photo_chronicle_shows_photos_ordered_by_date_desc()
    {
        // Create entries on different dates
        $day1 = DayEntry::create([
            'user_id' => $this->user->id,
            'date' => '2026-01-05',
        ]);

        $day2 = DayEntry::create([
            'user_id' => $this->user->id,
            'date' => '2026-01-10',
        ]);

        DayPhoto::create([
            'user_id' => $this->user->id,
            'day_entry_id' => $day1->id,
            'file_path' => 'photos/1/2026-01-05/old.jpg',
            'comment' => 'Old date',
        ]);

        DayPhoto::create([
            'user_id' => $this->user->id,
            'day_entry_id' => $day2->id,
            'file_path' => 'photos/1/2026-01-10/new.jpg',
            'comment' => 'New date',
        ]);

        $response = $this->actingAs($this->user)->get(route('photos.chronicle.index', ['month' => $this->testMonth]));

        $response->assertOk();
        // Newest date should appear first in HTML
        $content = $response->getContent();
        $newPos = strpos($content, 'New date');
        $oldPos = strpos($content, 'Old date');
        $this->assertLessThan($oldPos, $newPos, 'Newer date should appear before older date');
    }

    public function test_photo_chronicle_does_not_show_other_users_photos()
    {
        $otherUser = User::factory()->create();

        $dayEntry = DayEntry::create([
            'user_id' => $otherUser->id,
            'date' => '2026-01-06',
        ]);

        DayPhoto::create([
            'user_id' => $otherUser->id,
            'day_entry_id' => $dayEntry->id,
            'file_path' => 'photos/1/2026-01-06/secret.jpg',
            'comment' => 'Secret photo',
        ]);

        $response = $this->actingAs($this->user)->get(route('photos.chronicle.index', ['month' => $this->testMonth]));

        $response->assertOk();
        $response->assertDontSeeText('Secret photo');
    }

    public function test_photo_chronicle_month_filter_works()
    {
        $dayEntry = DayEntry::create([
            'user_id' => $this->user->id,
            'date' => '2026-01-06',
        ]);

        DayPhoto::create([
            'user_id' => $this->user->id,
            'day_entry_id' => $dayEntry->id,
            'file_path' => 'photos/1/2026-01-06/test.jpg',
            'comment' => 'January photo',
        ]);

        // Request February should show empty
        $response = $this->actingAs($this->user)->get(route('photos.chronicle.index', ['month' => '2026-02']));

        $response->assertOk();
        $response->assertDontSeeText('January photo');
        $response->assertSeeText('В этом месяце нет фотографий');
    }
}

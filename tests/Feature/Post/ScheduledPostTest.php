<?php

use App\Enums\Post\PostCategory;
use App\Enums\Post\PostStatus;
use App\Models\Post;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->createDefaultRoles();
    Storage::fake('public');
});

describe('Scheduled Posts — API validation', function () {

    it('creates a scheduled post with a future date and time', function () {
        $user = $this->createUser(['email' => 'scheduler@example.com']);

        $response = $this->actingAs($user)->postJson('/api/posts', [
            'title'     => 'Scheduled Announcement',
            'content'   => '<p>Coming soon.</p>',
            'category'  => PostCategory::ANNOUNCEMENT->value,
            'status'    => PostStatus::SCHEDULED->value,
            'post_date' => now()->addDay()->toDateString(),
            'post_time' => '09:00',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'post' => [
                        'status'    => PostStatus::SCHEDULED->value,
                        'post_date' => now()->addDay()->toDateString(),
                        'post_time' => '09:00:00',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'title'   => 'Scheduled Announcement',
            'status'  => PostStatus::SCHEDULED->value,
        ]);
    });

    it('returns a scheduled_at field combining post_date and post_time', function () {
        $user = $this->createUser(['email' => 'schedcheck@example.com']);
        $date = now()->addDays(2)->toDateString();

        $response = $this->actingAs($user)->postJson('/api/posts', [
            'title'     => 'Date Time Merge Test',
            'content'   => '<p>Testing scheduled_at.</p>',
            'category'  => PostCategory::GENERAL->value,
            'status'    => PostStatus::SCHEDULED->value,
            'post_date' => $date,
            'post_time' => '14:30',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.post.scheduled_at', $date . 'T14:30:00');
    });

    it('accepts post_time in H:i:s format', function () {
        $user = $this->createUser(['email' => 'timefmt@example.com']);

        $response = $this->actingAs($user)->postJson('/api/posts', [
            'title'     => 'Time Format Test',
            'content'   => '<p>Seconds included.</p>',
            'category'  => PostCategory::GENERAL->value,
            'status'    => PostStatus::SCHEDULED->value,
            'post_date' => now()->addDay()->toDateString(),
            'post_time' => '10:00:00',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.post.post_time', '10:00:00');
    });

    it('rejects a scheduled post missing post_date', function () {
        $user = $this->createUser(['email' => 'nodate@example.com']);

        $response = $this->actingAs($user)->postJson('/api/posts', [
            'title'    => 'No Date Post',
            'content'  => '<p>Missing date.</p>',
            'category' => PostCategory::GENERAL->value,
            'status'   => PostStatus::SCHEDULED->value,
            'post_time' => '10:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['post_date']);
    });

    it('rejects a scheduled post missing post_time', function () {
        $user = $this->createUser(['email' => 'notime@example.com']);

        $response = $this->actingAs($user)->postJson('/api/posts', [
            'title'     => 'No Time Post',
            'content'   => '<p>Missing time.</p>',
            'category'  => PostCategory::GENERAL->value,
            'status'    => PostStatus::SCHEDULED->value,
            'post_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['post_time']);
    });

    it('rejects a scheduled post with a past date', function () {
        $user = $this->createUser(['email' => 'pastdate@example.com']);

        $response = $this->actingAs($user)->postJson('/api/posts', [
            'title'     => 'Past Date Post',
            'content'   => '<p>Should fail.</p>',
            'category'  => PostCategory::GENERAL->value,
            'status'    => PostStatus::SCHEDULED->value,
            'post_date' => now()->subDay()->toDateString(),
            'post_time' => '10:00',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['post_date']);
    });

    it('rejects a scheduled post with an invalid time format', function () {
        $user = $this->createUser(['email' => 'badtime@example.com']);

        $response = $this->actingAs($user)->postJson('/api/posts', [
            'title'     => 'Bad Time Format',
            'content'   => '<p>Should fail.</p>',
            'category'  => PostCategory::GENERAL->value,
            'status'    => PostStatus::SCHEDULED->value,
            'post_date' => now()->addDay()->toDateString(),
            'post_time' => '25:99',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['post_time']);
    });

    it('allows updating a draft post to scheduled status', function () {
        $user = $this->createUser(['email' => 'updater@example.com']);

        $post = $user->posts()->create([
            'title'    => 'Draft Post',
            'content'  => '<p>Will be scheduled.</p>',
            'category' => PostCategory::GENERAL->value,
            'status'   => PostStatus::DRAFT->value,
        ]);

        $response = $this->actingAs($user)->patchJson('/api/posts/' . $post->id, [
            'status'    => PostStatus::SCHEDULED->value,
            'post_date' => now()->addDays(3)->toDateString(),
            'post_time' => '12:00',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.post.status', PostStatus::SCHEDULED->value);

        $this->assertDatabaseHas('posts', [
            'id'     => $post->id,
            'status' => PostStatus::SCHEDULED->value,
        ]);
    });
});

describe('Scheduled Posts — Artisan command', function () {

    it('publishes due scheduled posts when the command runs', function () {
        $user = $this->createUser(['email' => 'cmd@example.com']);

        // Due: date is in the past
        $pastPost = $user->posts()->create([
            'title'     => 'Past Scheduled Post',
            'content'   => '<p>Should be published.</p>',
            'category'  => PostCategory::GENERAL->value,
            'status'    => PostStatus::SCHEDULED->value,
            'post_date' => now()->subDay()->toDateString(),
            'post_time' => '08:00:00',
        ]);

        // Due: date is today, time has already passed (fixed past time to avoid midnight edge case)
        $todayPastPost = $user->posts()->create([
            'title'     => 'Today Past Time Post',
            'content'   => '<p>Also should be published.</p>',
            'category'  => PostCategory::GENERAL->value,
            'status'    => PostStatus::SCHEDULED->value,
            'post_date' => now()->toDateString(),
            'post_time' => '00:00:01',
        ]);

        // Not due: future date
        $futurePost = $user->posts()->create([
            'title'     => 'Future Scheduled Post',
            'content'   => '<p>Not yet.</p>',
            'category'  => PostCategory::GENERAL->value,
            'status'    => PostStatus::SCHEDULED->value,
            'post_date' => now()->addDay()->toDateString(),
            'post_time' => '09:00:00',
        ]);

        Artisan::call('posts:publish-scheduled');

        $this->assertDatabaseHas('posts', ['id' => $pastPost->id,     'status' => PostStatus::PUBLISHED->value]);
        $this->assertDatabaseHas('posts', ['id' => $todayPastPost->id,'status' => PostStatus::PUBLISHED->value]);
        $this->assertDatabaseHas('posts', ['id' => $futurePost->id,   'status' => PostStatus::SCHEDULED->value]);
    });

    it('does not touch draft or already-published posts', function () {
        $user = $this->createUser(['email' => 'nodraft@example.com']);

        $draft = $user->posts()->create([
            'title'    => 'Draft Post',
            'content'  => '<p>Draft.</p>',
            'category' => PostCategory::GENERAL->value,
            'status'   => PostStatus::DRAFT->value,
        ]);

        $published = $user->posts()->create([
            'title'     => 'Already Published',
            'content'   => '<p>Already live.</p>',
            'category'  => PostCategory::GENERAL->value,
            'status'    => PostStatus::PUBLISHED->value,
            'post_date' => now()->subDay()->toDateString(),
            'post_time' => '08:00:00',
        ]);

        Artisan::call('posts:publish-scheduled');

        $this->assertDatabaseHas('posts', ['id' => $draft->id,     'status' => PostStatus::DRAFT->value]);
        $this->assertDatabaseHas('posts', ['id' => $published->id, 'status' => PostStatus::PUBLISHED->value]);
    });

    it('outputs a friendly message when no posts are due', function () {
        $this->createUser(['email' => 'nopost@example.com']);

        Artisan::call('posts:publish-scheduled');

        $output = Artisan::output();
        expect($output)->toContain('No scheduled posts are due for publishing.');
    });

    it('outputs the count of published posts', function () {
        $user = $this->createUser(['email' => 'counttest@example.com']);

        $user->posts()->create([
            'title'     => 'Due Post 1',
            'content'   => '<p>Due.</p>',
            'category'  => PostCategory::GENERAL->value,
            'status'    => PostStatus::SCHEDULED->value,
            'post_date' => now()->subDay()->toDateString(),
            'post_time' => '08:00:00',
        ]);

        $user->posts()->create([
            'title'     => 'Due Post 2',
            'content'   => '<p>Also due.</p>',
            'category'  => PostCategory::GENERAL->value,
            'status'    => PostStatus::SCHEDULED->value,
            'post_date' => now()->subDay()->toDateString(),
            'post_time' => '09:00:00',
        ]);

        Artisan::call('posts:publish-scheduled');

        $output = Artisan::output();
        expect($output)->toContain('Published 2 scheduled post(s) successfully.');
    });
});

<?php

use App\Enums\Post\PostCategory;
use App\Enums\Post\PostStatus;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->createDefaultRoles();
    Storage::fake('public');
});

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function scheduledPost(int $userId, string $date, string $time): Post
{
    return Post::create([
        'user_id'   => $userId,
        'title'     => 'Scheduled Post',
        'content'   => '<p>Content.</p>',
        'category'  => PostCategory::GENERAL->value,
        'status'    => PostStatus::SCHEDULED->value,
        'post_date' => $date,
        'post_time' => $time,
    ]);
}

// ---------------------------------------------------------------------------
// Tests
// ---------------------------------------------------------------------------

describe('PostService::publishScheduledPosts', function () {

    it('publishes a post whose date is in the past', function () {
        $user = $this->createUser(['email' => 'past@unit.com']);
        $post = scheduledPost($user->id, now()->subDay()->toDateString(), '08:00:00');

        $count = app(PostService::class)->publishScheduledPosts();

        expect($count)->toBe(1);
        expect($post->fresh()->status)->toBe(PostStatus::PUBLISHED);
    });

    it('publishes a post scheduled for today with a time that has already passed', function () {
        $user = $this->createUser(['email' => 'today@unit.com']);
        // Use a fixed past time well before now to avoid second-boundary flakiness
        $post = scheduledPost($user->id, now()->toDateString(), '00:00:01');

        $count = app(PostService::class)->publishScheduledPosts();

        expect($count)->toBe(1);
        expect($post->fresh()->status)->toBe(PostStatus::PUBLISHED);
    });

    it('does not publish a post scheduled for today with a future time', function () {
        $user = $this->createUser(['email' => 'futuretime@unit.com']);
        $post = scheduledPost($user->id, now()->toDateString(), '23:59:59');

        $count = app(PostService::class)->publishScheduledPosts();

        expect($count)->toBe(0);
        expect($post->fresh()->status)->toBe(PostStatus::SCHEDULED);
    });

    it('does not publish a post with a future date', function () {
        $user = $this->createUser(['email' => 'futuredate@unit.com']);
        $post = scheduledPost($user->id, now()->addDay()->toDateString(), '08:00:00');

        $count = app(PostService::class)->publishScheduledPosts();

        expect($count)->toBe(0);
        expect($post->fresh()->status)->toBe(PostStatus::SCHEDULED);
    });

    it('returns the correct count when multiple posts are due', function () {
        $user = $this->createUser(['email' => 'multi@unit.com']);

        scheduledPost($user->id, now()->subDays(2)->toDateString(), '08:00:00');
        scheduledPost($user->id, now()->subDay()->toDateString(),  '10:00:00');
        scheduledPost($user->id, now()->toDateString(),            '00:00:01');

        $count = app(PostService::class)->publishScheduledPosts();

        expect($count)->toBe(3);
    });

    it('ignores draft and already-published posts', function () {
        $user = $this->createUser(['email' => 'mixed@unit.com']);

        $draft = $user->posts()->create([
            'title'    => 'Draft',
            'content'  => '<p>Draft.</p>',
            'category' => PostCategory::GENERAL->value,
            'status'   => PostStatus::DRAFT->value,
        ]);

        $alreadyPublished = $user->posts()->create([
            'title'     => 'Already Published',
            'content'   => '<p>Live.</p>',
            'category'  => PostCategory::GENERAL->value,
            'status'    => PostStatus::PUBLISHED->value,
            'post_date' => now()->subDay()->toDateString(),
            'post_time' => '08:00:00',
        ]);

        $dueScheduled = scheduledPost($user->id, now()->subDay()->toDateString(), '08:00:00');

        $count = app(PostService::class)->publishScheduledPosts();

        expect($count)->toBe(1);
        expect($draft->fresh()->status)->toBe(PostStatus::DRAFT);
        expect($alreadyPublished->fresh()->status)->toBe(PostStatus::PUBLISHED);
        expect($dueScheduled->fresh()->status)->toBe(PostStatus::PUBLISHED);
    });

    it('returns zero when no scheduled posts exist', function () {
        $count = app(PostService::class)->publishScheduledPosts();
        expect($count)->toBe(0);
    });

    it('is idempotent — running twice does not double-publish', function () {
        $user = $this->createUser(['email' => 'idempotent@unit.com']);
        $post = scheduledPost($user->id, now()->subDay()->toDateString(), '08:00:00');

        app(PostService::class)->publishScheduledPosts();
        $countSecond = app(PostService::class)->publishScheduledPosts();

        expect($countSecond)->toBe(0);
        expect($post->fresh()->status)->toBe(PostStatus::PUBLISHED);
    });
});

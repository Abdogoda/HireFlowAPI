<?php

namespace App\Console\Commands;

use App\Services\PostService;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';

    protected $description = 'Publish all scheduled posts whose date and time have arrived';

    public function handle(PostService $postService): int
    {
        $count = $postService->publishScheduledPosts();

        if ($count === 0) {
            $this->info('No scheduled posts are due for publishing.');
        } else {
            $this->info("Published {$count} scheduled post(s) successfully.");
        }

        return self::SUCCESS;
    }
}

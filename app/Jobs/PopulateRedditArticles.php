<?php

namespace App\Jobs;

use App\Http\Controllers\Api\RedditController;
use App\Models\Article;
use App\Services\RedditService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PopulateRedditArticles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $RedditService;
    protected $appCategory;
    protected $subReddit;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($appCategory = '', $subReddit = '')
    {
        // Log::info("appCategory: " . $appCategory);
        // Log::info("subReddit: " . $subReddit);
        $this->RedditService = app(RedditService::class);
        $this->appCategory = $appCategory;
        $this->subReddit = $subReddit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $returnArray = $this->RedditService->getArticles($this->subReddit) ?? [];
        // echo 'returnObj: ' . print_r($returnObj,true);  

        if (is_array($returnArray) && is_countable($returnArray) && count($returnArray) > 0) {
            for ($i = 0; $i < count($returnArray); $i++) {
                $item = $returnArray[$i] ?? null;

                if ($item) {
                    try {
                        $entry = Article::create([
                            'title' => substr($item['title'], 0, 255),
                            'image_url' => $item['image_url'],
                            'source' => $item['source'],
                            'app_category' => $this->appCategory,
                            'subreddit' => $this->subReddit,
                            'created_utc' => $item['created_utc'],
                            'author' => $item['author'] ?? null,
                            'url' => $item['url'],
                            'reddit_article_id' => $item['reddit_article_id'],
                            'app_id_fk' => 1,
                        ]);

                        if ($entry->id) {
                            echo 'item: ' . print_r($item, true);
                        }
                        // echo 'id: ' . $entry->id;
                        // return $entry->id;

                    } catch (\Illuminate\Database\QueryException $exception) {

                        // 23000 = SQLSTATE[23000]: Integrity constraint violation
                        // Not interested in logging Integrity constraint violations
                        if ($exception->getCode() != 23000) {
                            // Log::error("insertCommunityEntry Error Message: " . $exception->getCode());
                            Log::error("PopulateRedditArticles - handle() Error Message: " . $exception->getMessage());
                        }
                    }
                }
            }
        }
    }
}

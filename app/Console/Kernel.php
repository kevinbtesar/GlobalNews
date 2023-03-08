<?php

namespace App\Console;

use App\Jobs\PopulateRedditArticles;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        foreach(config("enums.reddit_globalnews_categories") as $appCategory => $category){

            foreach($category['subReddits'] as $subReddit){
                echo  PHP_EOL . PHP_EOL .'appCategory: ' .$category['title'];
                echo  PHP_EOL . PHP_EOL .'subReddit: ' . $subReddit;

                $schedule->job(new PopulateRedditArticles)->withoutOverlapping()->everyMinute(); // TODO: increase for Production
                PopulateRedditArticles::dispatch( $category['title'],  $subReddit)
                    ->delay(now()->addSeconds(20)); // TODO: increase for Production

            }

            //  delays loop. used for obeying rate limits
            sleep(20); // TODO: increase for Production
        
    }
   

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }


}

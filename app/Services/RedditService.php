<?php

namespace App\Services;

use App\Models\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class RedditService
{

    public function getArticles($subreddit)
    {
        $json = [];
        $error = null;
        $result = null;


        try {
            $api = Api::where('name', 'reddit')->first();
            $response = Http::withToken($api->access_token)
                ->get(config("services.reddit.api_url") . "/r/" . $subreddit . "/" . config("enums.reddit_sortby_type.HOT"));

            //TODO: /rising
            // Log::info('FIRST $result->body: ' . print_r($response->body(),true));
            $result = json_decode($response->body());

            if (json_last_error() === JSON_ERROR_NONE && $result !== null && is_object($result)) {

                // Log::info('GETTING REDDIT ARTICLES - FIRST ATTEMPT - SUCCESSFUL');
                $result = json_decode(json_encode($result->data->children), true);
            } else {

                // Log::info('REDDIT - JSON DECODING ERROR - REFRESHING ACCESS TOKEN');

                $result = self::refreshRedditAccessToken($api);
                $api = Api::where('name', 'reddit')->first();

                $result = Http::withToken($api->access_token)
                    ->get(config("services.reddit.api_url") . "/r/" . $subreddit . "/" . config("enums.reddit_sortby_type.HOT"));

                $result = json_decode($result->body());

                if (json_last_error() === JSON_ERROR_NONE && $result !== null && is_object($result) && $result->data) {
                    // Log::info('GETTING REDDIT ARTICLES - SECOND ATTEMPT - SUCCESSFUL');
                    $result = json_decode(json_encode($result->data->children), true);
                } else {
                    $error = 'RedditController JSON DECODING ERROR after second attempt.' . PHP_EOL . PHP_EOL . '$response: ' . print_r($result->body(), true);
                }
            }

            // echo 'result1: ' . print_r($result, true);

            /**
             * if $result is an array (successful fetch), 
             * construct the return $json value
             */
            if ($result !== null && is_array($result)) {

                $result = self::constructReturnArray($result);
            } else {
                $error = '$json was not an array.' . PHP_EOL . PHP_EOL . '$response: ' . print_r($response->body(), true);
            }
        } catch (\Exception $ex) {
            $error = 'RedditController getArticles() Error Message: ' . $ex->getMessage() . PHP_EOL . PHP_EOL . '$response: ' . print_r($result, true);
        }
        // }
        // echo 'result2: ' . print_r($result, true);
        return $result;
    }


    /**
     * Get a new access token using refresh token
     *
     * @param  Request object
     * @return json object
     */
    public function refreshRedditAccessToken($api)
    {
        try {

            $response = Http::withBasicAuth(env('REDDIT_CLIENT_ID'), env('REDDIT_CLIENT_SECRET'))
                ->asForm()
                ->post(config("services.reddit.token_url"), [
                    'client_id' => env('REDDIT_CLIENT_ID'),
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $api->refresh_token,
                ]);

            $result = json_decode($response->body(), true);
            // Log::info("HERE " . print_r($result, true));

            if (isset($result) && isset($result['access_token'])) {
                Api::where('name', 'reddit')->update(['access_token' => $result['access_token']]);
            } else {
                Log::error("refreshRedditAccessToken - else error: " . print_r($result, true));
            }

            return $result;
        } catch (\Exception $ex) {
            $error = 'refreshRedditAccessToken Error Message: ' . $ex->getMessage() . PHP_EOL . PHP_EOL . '$response: ' . $response;
            Log::error($error);
            return $error;
        }
    }



    /**
     * Construct return json in a universal format
     *
     * @param  Request object
     * @return Array
     */
    public function constructReturnArray($json)
    {
        // echo 'json: ' . print_r($json, true);
        $returnJson = [];
        for ($i = 0; $i < count($json); $i++) {
            $item = $json[$i]['data'] ?? null;

            if (
                $item &&
                isset($item['preview']) &&
                isset($item['url']) &&
                str_contains($item['url'], 'http') &&
                str_contains($item['url'], $item['domain']) &&

                !str_contains($item['domain'], 'redd.it') &&
                !str_contains($item['domain'], 'facebook.com') &&
                !str_contains($item['domain'], 'imgur.com') &&
                !str_contains($item['domain'], 'docs.google.com') &&
                !str_contains($item['domain'], 'twitter.com') &&
                !str_contains($item['domain'], 'youtu.be') &&
                !str_contains($item['domain'], 'nytimes.com') &&
                !str_contains($item['domain'], 'washingtonpost.com') &&
                !str_contains($item['domain'], 'youtube.com') &&
                !str_contains($item['domain'], 'wikipedia.org')

            ) {
                $imgUrl = json_decode(json_encode($item['preview']));
                // Log::info($imgUrl->images[0]->source->url);

                $returnJson[$i]['author'] = $item['author_fullname'] ?? '';
                $returnJson[$i]['title'] = $item['title'];
                // $returnJson[$i]['thumbnail'] = $item['thumbnail'];
                $returnJson[$i]['image_url'] = str_replace('&amp;', '&', $imgUrl->images[0]->source->url);
                $returnJson[$i]['created_utc'] = $item['created_utc'];
                $returnJson[$i]['source'] = $item['domain'];
                $returnJson[$i]['reddit_article_id'] = $item['id'];
                $returnJson[$i]['url'] = $item['url'];
            }
        }
        // echo 'returnJson: ' . print_r($returnJson, true);
        return $returnJson;
    }
}

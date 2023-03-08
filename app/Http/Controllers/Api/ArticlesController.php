<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArticlesController extends \App\Http\Controllers\Controller
{
    public function getArticles(Request $request)
    {
        // Log::info('ZERO ' . $request->appName);
        // Log::info('ZERO-1 ' . $request->category);

        $returnArticlesJson = [];
        $returnCategoriesJson = []; 
        $error = null;

        if ($request && $request->appName == 'Global News') 
        {
            foreach(config("enums.reddit_globalnews_categories") as $category){

                $articles = Article::where('app_id_fk',  1)
                    ->where('app_category', $category['title'])
                    ->limit(20)
                    ->orderBy('created_utc', 'desc');

                // Log::info("toSql: " . print_r( $articles->toSql(),true));
                // Log::info("getBindings: " . print_r($articles->getBindings(),true));

                $articles = $articles->get();
                $result = json_decode(json_encode($articles), true);                
                $returnArticlesJson = (object) array_merge((array) $returnArticlesJson, (array) $result);
            }
            
            // optional cast to an array. note: cast is optional if not adding test article
            // $returnArticlesJson = (array) $returnArticlesJson; 
            // (object) array_push($returnArticlesJson, (array) self::addTestArticle()); //manually add test article
            
            foreach(config("enums.reddit_globalnews_categories") as $category){
                // Log::info("subReddits: " . print_r($subReddit,true));
                $returnCategoriesJson[] = (object) ['title'=> $category['title']];
            }
           
            return response()->json([
                'articles' => (object) $returnArticlesJson,
                'categories' => $returnCategoriesJson 
                
            ], 201);
          
        } else {
            $error = 'There was an issue finding the GET parameters.';
            Log::error($error);
            return response()->json(['error' => $error], 500);
        }
    }


    public function addTestArticle()
    {
        $re = new \stdClass();
        $re->author = 'test';
        $re->title = 'test';
        $re->thumbnail = 'test';
        $re->app_category = 'Global';
        $re->image_url ='https://external-preview.redd.it/UCkque76TKRcfFXQH2hprly2ny1szg_505xHkbH9J-s.jpg?auto=webp&s=978e6a69d7ab6370e587e2333b3af702ec2af513';
        $re->created_utc = 343423324;
        $re->domain = 'test';
        return $re;
    }

}

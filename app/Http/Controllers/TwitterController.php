<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tweet;

/**
 * TwitterController
 */
class TwitterController extends Controller
{
    /**
     * index
     *
     * @param Request $request
     * @return view
     */
    public function index(Request $request)
    {
        try {
            \Log::info(__FILE__.':'.__LINE__.':'.__CLASS__.':'. __FUNCTION__.':entry');

            return view('twitter_index', [
                "result" => ''
            ]);
        } catch (\Throwable $th) {
            \Log::error($th);
        } finally {
            \Log::info(__FILE__.':'.__LINE__.':'.__CLASS__.':'. __FUNCTION__.':exit');
        }
    }

    /**
     * search
     *
     * @param Request $request
     * @return view
     */
    public function search(Request $request)
    {
        try {
            \Log::info(__FILE__.':'.__LINE__.':'.__CLASS__.':'. __FUNCTION__.':entry');

            $results = Tweet::tweetSearch($request);

            return view('twitter_index', [
                "result" => $results
            ]);
        } catch (\Throwable $th) {
            \Log::error($th);
        } finally {
            \Log::info(__FILE__.':'.__LINE__.':'.__CLASS__.':'. __FUNCTION__.':exit');
        }
    }
}

<?php

namespace App\Models\Traits;

use App\Models\Topic;
use App\Models\Reply;
use Carbon\Carbon;
use Cache;
use DB;
use Arr;

trait ActiveUserHelper
{
    // Used to temporarily store user data
    protected $users = [];

    // basic parameters
    protected $topic_weight = 4; // weight of posting a topic
    protected $reply_weight = 1; // weight of posting a reply
    protected $pass_days = 7; // time interval (in days) of new content being posted
    protected $user_number = 6; // how many users to retrieve

    // cache related data
    protected $cache_key = 'larabbs_active_users';
    protected $cache_expire_in_seconds = 65 * 60;

    public function getActiveUsers()
    {
        // get data cached using cache_key
        // if data exists, return immediately
        // otherwise, get active user data through anonymous function, and caching result upon return
        return Cache::remember($this->cache_key, $this->cache_expire_in_seconds, function () {
            return $this->calculateActiveUsers();
        });
    }

    public function calculateAndCacheActiveUsers()
    {
        // get active users list
        $active_users = $this->calculateActiveUsers();

        // cache the users
        $this->cacheActiveUsers($active_users);
    }

    private function calculateActiveUsers()
    {
        $this->calculateTopicScore();
        $this->calculateReplyScore();

        // sort array based on score
        $users = Arr::sort($this->users, function($user) {
            return $user['score'];
        });

        // array needs to be in descending order based on score
        // second parameter decides whether or not to keep the array's keys as is
        $users = array_reverse($users, true);

        // only keep the number of users we want
        $users = array_slice($users, 0, $this->user_number, true);

        // create a new empty collection
        $active_users = collect();

        foreach($users as $user_id => $user){
            // check to see if user exists
            $user = $this->find($user_id);

            // if user exists in database
            if($user){
                // push user instance onto collection
                $active_users->push($user);
            }
        }

        return $active_users;
    }

    private function calculateTopicScore()
    {
        // find all the users who published topics from topics table within $pass_days,
        // and get the number of topics that the users have posted within the period
        $topic_users = Topic::query()->select(DB::raw('user_id, count(*) as topic_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')
            ->get();

        // calculate score based on amount of topics posted
        foreach($topic_users as $value){
            $this->users[$value->user_id]['score'] = $value->topic_count * $this->topic_weight;
        }
    }

    private function calculateReplyScore()
    {
        // Get all users who published replies from replies table within $pass_days,
        // and get the number of replies that they've posted within the period
        $reply_users = Reply::query()->select(DB::raw('user_id, count(*) as reply_count'))
            ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
            ->groupBy('user_id')
            ->get();

        // calculate score based on amount of replies posted
        foreach($reply_users as $value){
            $reply_score = $value->reply_count * $this->reply_weight;
            if(isset($this->users[$value->user_id])){
                $this->users[$value->user_id]['score'] += $reply_score;
            }else{
                $this->users[$value->user_id]['score'] = $reply_score;
            }
        }
    }

    private function cacheActiveUsers($active_users)
    {
        // cache active users' data
        Cache::put($this->cache_key, $active_users, $this->cache_expire_in_seconds);
    }
}

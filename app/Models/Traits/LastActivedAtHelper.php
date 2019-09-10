<?php

namespace App\Models\Traits;

use Redis;
use Carbon\Carbon;

trait LastActivedAtHelper
{
    // Cache related
    protected $hash_prefix = 'larabbs_last_actived_at_';
    protected $field_prefix = 'user_';

    public function recordLastActivedAt()
    {
        // Redis hashtable name, eg. larabbs_last_actived_at_2017-10-21
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());

        // Field name, eg. user_1
        $field = $this->getHashField();

        // current time
        $now = Carbon::now()->toDateTimeString();

        // write data into redis, any existing fields will be updated
        Redis::hSet($hash, $field, $now);
    }

    public function syncUserActivedAt()
    {
        // Redis hashtable name, eg. larabbs_last_actived_at_2017-10-21
        $hash = $this->getHashFromDateString(Carbon::yesterday()->toDateString());

        // get all data from hashtable
        $dates = Redis::hGetAll($hash);

        // traverse and sync data to database
        foreach($dates as $user_id => $actived_at){
            // convert `user_1` to `1`
            $user_id = str_replace($this->field_prefix, '', $user_id);

            // only save to database if user exists
            if($user = $this->find($user_id)){
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }

        // after syncing with database (central repo), delete from cache
        Redis::del($hash);
    }

    public function getLastActivedAtAttribute($value)
    {
        // Redis hashtable name, eg. larabbs_last_actived_at_2017-10-21
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());

        // field name, eg. user_1
        $field = $this->getHashField();

        // Look for data inside redis, if nothing exists then search database
        $datetime = Redis::hGet($hash, $field) ? : $value;

        // If datetime exists, return corresponding Carbon instance
        if($datetime){
            return new Carbon($datetime);
        }else{
            // otherwise use user created_at time
            return $this->created_at;
        }
    }

    public function getHashFromDateString($date)
    {
        // Redis hashtable name, eg. larabbs_last_actived_at_2017-10-21
        return $this->hash_prefix . $date;
    }

    public function getHashField()
    {
        // field name, eg. user_1
        return $this->field_prefix . $this->id;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;

class Link extends Model
{
    protected $fillable = ['title', 'link'];

    public $cache_key = 'larabbs_links';
    protected $cache_expire_in_seconds = 1440 * 60;

    public function getAllCached()
    {
        // look for data in the cache using cache_key, and return data if found
        // otherwise look inside links table via anonymous function, and cache result before returning
        return Cache::remember($this->cache_key, $this->cache_expire_in_seconds, function () {
            return $this->all();
        });
    }
}

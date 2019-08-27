<?php

namespace App\Models;

class Topic extends Model
{
    protected $fillable = ['title', 'body', 'category_id', 'excerpt', 'slug'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeWithOrder($query, $order)
    {
        switch($order){
            case 'recent':
                $query->recent();
                break;

            default:
                $query->recentReplied();
                break;
        }

        // prevent N+1 loading issue
        return $query->with('user', 'category');
    }

    public function scopeRecentReplied($query)
    {
        // whenever a topic gets a new reply, reply_count property will be updated
        // at which time updated_at timestamp will be updated as well (via trigger)
        return $query->orderBy('updated_at', 'desc');
    }

    public function scopeRecent($query)
    {
        // order by create time
        return $query->orderBy('created_at', 'desc');
    }

    public function link($params = [])
    {
        return route('topics.show', array_merge([$this->id, $this->slug], $params));
    }
}

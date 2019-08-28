<?php

namespace App\Observers;

use App\Models\Topic;
//use App\Handlers\SlugTranslateHandler;
use App\Jobs\TranslateSlug;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function creating(Topic $topic)
    {
        //
    }

    public function updating(Topic $topic)
    {
        //
    }

    public function saving(Topic $topic)
    {
        // XSS Filtering
        $topic->body = clean($topic->body, 'user_topic_body');

        // Generate topic excerpt
        $topic->excerpt = make_excerpt($topic->body);

    }

    public function saved(Topic $topic)
    {
        // if slug column is empty, translate title
        if(!$topic->slug){
            //$topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);
            dispatch(new TranslateSlug($topic));
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\Category;
use App\Models\User;
use App\Models\Link;

class CategoriesController extends Controller
{
    public function show(Category $category, Request $request, Topic $topic, User $user, Link $link)
    {
        // retrieve topics corresponding to category id, paginate with 20 records each page
        $topics = $topic->withOrder($request->order)
            ->where('category_id', $category->id)
            ->paginate(20);

        // get active users
        $active_users = $user->getActiveUsers();

        // recommended links
        $links = $link->getAllCached();

        // pass both topics and category to view
        return view('topics.index', compact('topics', 'category', 'active_users', 'links'));
    }
}

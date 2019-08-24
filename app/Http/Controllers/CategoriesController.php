<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\Category;

class CategoriesController extends Controller
{
    public function show(Category $category)
    {
        // retrieve topics corresponding to category id, paginate with 20 records each page
        $topics = Topic::where('category_id', $category->id)->paginate(20);

        // pass both topics and category to view
        return view('topics.index', compact('topics', 'category'));
    }
}

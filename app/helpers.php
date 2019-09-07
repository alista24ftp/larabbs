<?php
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

function category_nav_active($category_id)
{
    return active_class((if_route('categories.show')
        && if_route_param('category', $category_id)));
}

function make_excerpt($value, $length=200)
{
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));
    return str_limit($excerpt, $length);
}

function model_admin_link($title, $model)
{
    return model_link($title, $model, 'admin');
}

function model_link($title, $model, $prefix='')
{
    // get snakecase name of model class in plural form
    $model_name = model_plural_name($model);

    // initialize prefix
    $prefix = $prefix ? '/' . $prefix . '/' : '/';

    // use app URL to generate full URL
    $url = config('app.url') . $prefix . $model_name . '/' . $model->id;

    // generate HTML A tag, and return
    return '<a href="' . $url . '" target="_blank">' . $title . '</a>';
}

function model_plural_name($model)
{
    // get full class name from instance (eg. App\Models\User)
    $full_class_name = get_class($model);

    // get class basename (eg. App\Models\User => User)
    $class_name = class_basename($full_class_name);

    // get snakecase name (eg. User => user, FooBar => foo_bar)
    $snake_case_name = snake_case($class_name);

    // get plural form of a string
    return str_plural($snake_case_name);
}

<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasRoles;
    use MustVerifyEmailTrait;

    use Notifiable {
        notify as protected laravelNotify;
    }

    public function notify($instance)
    {
        // if user to be notified is the same as current logged in user,
        // then no need to notify
        if($this->id == Auth::id()) return;

        // only notify if notification channel is of database type,
        // pass on email or other channels
        if(method_exists($instance, 'toDatabase')){
            $this->increment('notification_count');
        }

        $this->laravelNotify($instance);
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'introduction', 'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    /*
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    */

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    public function setPasswordAttribute($value)
    {
        // If length of value is 60, it is assumed to be hashed
        if(strlen($value) != 60){
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }

    public function setAvatarAttribute($path)
    {
        // If path doesn't start with `http`, then it's uploaded from backend
        // we need full URL
        if(!starts_with($path, 'http')){
            // create full URL
            $path = config('app.url') . "/uploads/images/avatars/$path";
        }

        $this->attributes['avatar'] = $path;
    }
}

<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });
    }

    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    public function feed()
    {
        $userIds = Auth::user()->followings->pluck('id')->toArray();
        array_push($userIds, Auth::user()->id);

        return Status::whereIn('user_id', $userIds)
                                ->with('user')
                                ->orderBy('created_at', 'desc');
    }

    /**
     * 粉丝
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    /**
     * 关注的人
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    /**
     * 添加到关注
     *
     * @param $userIds
     */
    public function follow($userIds)
    {
        if (!is_array($userIds)) {
            $userIds = compact('userIds');
        }

        $this->followings()->sync($userIds, false);
    }

    /**
     * 取消关注
     *
     * @param $userIds
     */
    public function unFollow($userIds)
    {
        if (!is_array($userIds)) {
            $userIds = compact('userIds');
        }

        $this->followings()->detach($userIds);
    }

    public function isFollowing($userId)
    {
        return $this->followings->contains($userId);
    }
}

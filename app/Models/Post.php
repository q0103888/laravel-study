<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // 모델은 하나의 레코드이다.
    
    use HasFactory;

    public function imagePath() {
       // $path = '/storage/images';
        $path = env('IMAGE_PATH', '/storage/image/');
        $imageFile = $this -> image ?? 'no_image_available.png';
        return $path.$imageFile;
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function viewers() {
        //return $this->belongsToMany(User::class);
        return $this->belongsToMany(User::class, 'post_user','post_id','user_id','id','id','users');
    }
}

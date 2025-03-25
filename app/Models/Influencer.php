<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Influencer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image'];

    public function socialMedias()
    {
        return $this->belongsToMany(SocialMedia::class, 'influencers_social_medias', 'influencer_id', 'social_medias_id')
            ->withPivot('price')
            ->withTimestamps();
    }
}


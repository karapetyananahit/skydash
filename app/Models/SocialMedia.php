<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    use HasFactory;

    protected $table = 'social_medias';
    protected $fillable = ['name'];

    public function influencers()
    {
        return $this->belongsToMany(Influencer::class, 'influencers_social_medias', 'social_medias_id', 'influencer_id')
            ->withPivot('price')
            ->withTimestamps();
    }
}


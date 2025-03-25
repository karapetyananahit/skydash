<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfluencerSocialMedia extends Model
{
    use HasFactory;

    protected $fillable = ['influencer_id', 'social_medias_id', 'price'];

    public function influencer()
    {
        return $this->belongsTo(Influencer::class);
    }

    public function socialMedia()
    {
        return $this->belongsTo(SocialMedia::class);
    }
}

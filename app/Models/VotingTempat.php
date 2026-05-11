<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotingTempat extends Model
{
    protected $table    = 'voting_tempat';
    protected $fillable = ['nama', 'lokasi', 'deskripsi', 'foto', 'is_active'];
    protected $casts    = ['is_active' => 'boolean'];

    public function votes()
    {
        return $this->hasMany(VotingVote::class);
    }

    public function getVoteCountAttribute(): int
    {
        return $this->votes()->count();
    }
}
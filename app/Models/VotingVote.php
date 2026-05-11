<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotingVote extends Model
{
    protected $table    = 'voting_votes';
    protected $fillable = ['karyawan_nik', 'voting_tempat_id'];

    public function tempat()
    {
        return $this->belongsTo(VotingTempat::class, 'voting_tempat_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_nik', 'nik');
    }
}
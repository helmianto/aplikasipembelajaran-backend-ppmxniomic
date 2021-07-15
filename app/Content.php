<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = ['judul', 'keterangan', 'link_thumbnail', 'link_video', 'status', 'view'];
}

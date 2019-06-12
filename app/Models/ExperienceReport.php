<?php

namespace App\Models;

class ExperienceReport extends ApiBaseModel
{
    protected $table = 'experience_reports';

    protected $fillable = [
        'type', 'type_id', 'content', 'image', 'like_times'
    ];


}
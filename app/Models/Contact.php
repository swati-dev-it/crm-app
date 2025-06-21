<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'gender', 'profile_image', 'additional_file', 'is_merged', 'merged_into_id'];
    
    public function customFieldValues() 
    {
        return $this->hasMany(ContactCustomFieldValue::class);
    }

    public function mergedFrom()
    {
        return $this->hasMany(Contact::class, 'merged_into_id');
    }

    public function mergedInto()
    {
        return $this->belongsTo(Contact::class, 'merged_into_id');
    }
}

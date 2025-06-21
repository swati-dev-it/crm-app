<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactCustomFieldValue extends Model
{
    protected $fillable = ['contact_id', 'custom_field_id', 'value'];
    
    public function customField() {
        return $this->belongsTo(CustomField::class, 'custom_field_id');
    }
}

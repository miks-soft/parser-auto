<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    protected $fillable = [
        'slug', 'value', 'value_type', 'config', 'title',
    ];

    public $timestamps = false;

    public function getValueAttribute($value)
    {
        switch ($this->value_type)
        {
            case 'integer':
            {
                return (int)$value;
            }
            case 'float':
            {
                return (float)$value;
            }
            case 'boolean':
            {
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
            default:break;
        }
        return $value;
    }
    public function setValueAttribute($value)
    {
        switch ($this->value_type)
        {
            case 'integer':
            {
                $value = (int)$value;
                break;
            }
            case 'float':
            {
                $value = (float)$value;
                break;
            }
            case 'boolean':
            {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;
            }
            default:break;
        }
        $this->attributes['value'] = $value;
    }
}

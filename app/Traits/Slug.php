<?php


namespace App\Traits;


use Illuminate\Support\Str;

trait Slug
{
    public function setNameAttribute($value)
    {
        $slug = Str::slug($value);

        $this->attributes['name'] = $value;
        $matches = $this->uniqueSlug($slug);
        $this->attributes['slug'] = $matches ? $slug . '-' .  $matches : $slug ;
    }
    public function  uniqueSlug($slug)
    {
        $matches = $this->whereRaw("slug REGEXP '^{$slug}(-[0-9]*)?$'")->count();
        return $matches;
    }
}

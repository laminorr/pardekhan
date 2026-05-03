<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'seo_title',
        'seo_description',
        'hero_kicker',
        'hero_title',
        'hero_lead',
        'key_concepts',
        'related_tags',
        'featured_episode_slugs',
        'sections',
        'faq',
        'is_published',
        'sort_order',
        'published_at',
    ];

    protected $casts = [
        'key_concepts' => 'array',
        'related_tags' => 'array',
        'featured_episode_slugs' => 'array',
        'sections' => 'array',
        'faq' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('title');
    }
}

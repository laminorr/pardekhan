<?php

namespace App\Console\Commands;

use App\Models\Episode;
use App\Models\Theme;
use App\Models\Lesson;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportEpisode extends Command
{
    protected $signature = 'episode:import {file : Path to JSON file}';
    protected $description = 'Import an episode from a JSON file';

    public function handle(): int
    {
        $path = $this->argument('file');

        if (! file_exists($path)) {
            $this->error("File not found: {$path}");
            return 1;
        }

        $json = json_decode(file_get_contents($path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON: ' . json_last_error_msg());
            return 1;
        }

        $this->info("Importing episode: {$json['title_fa']} ...");

        DB::transaction(function () use ($json) {
            // Create episode
            $episode = Episode::updateOrCreate(
                ['slug' => $json['slug']],
                [
                    'episode_number'        => $json['episode_number'],
                    'title_fa'              => $json['title_fa'],
                    'title_en'              => $json['title_en'],
                    'year'                  => $json['year'],
                    'director'              => $json['director'],
                    'imdb_url'              => $json['imdb_url'] ?? null,
                    'aparat_hash'           => $json['aparat_hash'] ?? null,
                    'hero_title_html'       => $json['hero_title_html'],
                    'hero_lead'             => $json['hero_lead'],
                    'essay_title_html'      => $json['essay_title_html'],
                    'essay_paragraphs'      => $json['essay_paragraphs'],
                    'opening_quote_text'    => $json['opening_quote_text'],
                    'opening_quote_cite'    => $json['opening_quote_cite'],
                    'essay_after_paragraphs'=> $json['essay_after_paragraphs'] ?? null,
                    'big_quote_highlight'   => $json['big_quote_highlight'],
                    'big_quote_rest'        => $json['big_quote_rest'],
                    'big_quote_source'      => $json['big_quote_source'],
                    'meta_duration'         => $json['meta_duration'] ?? '00:00',
                    'meta_approaches_count' => $json['meta_approaches_count'] ?? 4,
                    'meta_references_count' => $json['meta_references_count'] ?? 0,
                    'meta_quotes_count'     => $json['meta_quotes_count'] ?? 0,
                    'meta_level'            => $json['meta_level'] ?? 'پیشرفته',
                    'meta_tags'             => $json['meta_tags'] ?? [],
                    'next_episode_number'   => $json['next_episode_number'] ?? null,
                    'next_episode_title'    => $json['next_episode_title'] ?? null,
                    'next_episode_subtitle' => $json['next_episode_subtitle'] ?? null,
                    'seo_title'             => $json['seo_title'] ?? null,
                    'seo_description'       => $json['seo_description'] ?? null,
                    'is_published'          => $json['is_published'] ?? true,
                    'published_at'          => $json['published_at'] ?? now(),
                ]
            );

            // Sync themes
            if (isset($json['themes'])) {
                $episode->themes()->delete();
                foreach ($json['themes'] as $i => $t) {
                    $episode->themes()->create([
                        'sort_order'         => $i,
                        'number_label'       => $t['number_label'],
                        'title'              => $t['title'],
                        'approach'           => $t['approach'],
                        'paragraph'          => $t['paragraph'],
                        'quote'              => $t['quote'],
                        'reference_fa'       => $t['reference_fa'],
                        'reference_en'       => $t['reference_en'],
                        'simple_explanation' => $t['simple_explanation'],
                    ]);
                }
                $this->info("  ✓ {$episode->themes()->count()} themes imported");
            }

            // Sync lessons
            if (isset($json['lessons'])) {
                $episode->lessons()->delete();
                foreach ($json['lessons'] as $i => $l) {
                    $episode->lessons()->create([
                        'sort_order'  => $i,
                        'title'       => $l['title'],
                        'description' => $l['description'],
                        'example'     => $l['example'],
                    ]);
                }
                $this->info("  ✓ {$episode->lessons()->count()} lessons imported");
            }

            $this->info("  ✓ Episode #{$episode->episode_number} saved");
            $this->info("  → URL: " . url($episode->slug));
        });

        $this->newLine();
        $this->info('✅ Import complete!');

        return 0;
    }
}

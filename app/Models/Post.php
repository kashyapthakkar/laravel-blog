<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Post
{
    public $title;
    public $exerpt;
    public $date;
    public $body;
    public $slug;

    public function __construct($title, $exerpt, $date, $body, $slug)
    {
        $this->title = $title;
        $this->exerpt = $exerpt;
        $this->date = $date;
        $this->body = $body;
        $this->slug = $slug;
    }

    public static function find($slug)
    {
        return static::all()->firstWhere('slug', $slug);
    }

    public static function all()
    {

        $files = File::files(resource_path("posts/"));

        return cache()->rememberForever('posts.all', function () use ($files) {
            return collect($files)
                ->map(fn ($file) => YamlFrontMatter::parseFile($file))
                ->map(
                    fn ($document) => new Post(
                        $document->title,
                        $document->exerpt,
                        $document->date,
                        $document->body(),
                        $document->slug
                    )
                )->sortByDesc('date');
        });
    }
}

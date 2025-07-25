<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        News::create([
            'title' => 'Sample News Title',
            'content' => 'This is a sample content for the news article.',
            'image' => 'images/sample.jpg', // Assuming the image is stored in the public/images directory
        ]);
    }
}

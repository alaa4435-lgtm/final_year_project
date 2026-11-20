<?php

namespace Database\Seeders;

use App\Models\Stage;
use App\Models\Template;
use Illuminate\Database\Seeder;

class StageSeeder extends Seeder
{
    public function run(): void
    {
        Stage::updateOrCreate(['template_id' => null, 'slug' => 'intro-web-dev'], [
            'title' => 'Introduction to Web Development',
            'order' => 0,
            'description' => 'Learn the basics of HTML, CSS, and JavaScript before starting the project.',
            'is_global' => true,
        ]);

        $stagesByTemplate = [
            'portfolio' => [
                ['Navigation Bar', 'navbar'],
                ['Hero Section',   'hero'],
                ['About Section',  'about'],
                ['Projects',       'projects'],
                ['Contact',        'contact'],
                ['Footer',         'footer'],
            ],
            'landing-page' => [
                ['Header',         'header'],
                ['Hero',           'hero'],
                ['Features',       'features'],
                ['Call to Action', 'cta'],
                ['Footer',         'footer'],
            ],
        ];

        foreach ($stagesByTemplate as $templateSlug => $stages) {
            $template = Template::where('slug', $templateSlug)->firstOrFail();

            foreach ($stages as $index => [$title, $slug]) {
                Stage::updateOrCreate(['template_id' => $template->id, 'slug' => $slug], [
                    'title' => $title,
                    'order' => $index + 1,
                    'is_global' => false,
                ]);
            }
        }
    }
}

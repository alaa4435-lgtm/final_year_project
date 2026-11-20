<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        Template::updateOrCreate(['slug' => 'portfolio'], [
            'name' => 'Portfolio Website',
            'description' => 'Build your personal website to showcase your skills and projects.',
            'is_active' => true,
        ]);

        Template::updateOrCreate(['slug' => 'landing-page'], [
            'name' => 'Landing Page',
            'description' => 'Build a professional landing page for a product or service.',
            'is_active' => true,
        ]);
    }
}

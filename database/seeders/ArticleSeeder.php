<?php

namespace Database\Seeders;

use App\Models\Article;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        for ($i = 0; $i < 100000; $i++) {

            $article = new Article();
            $article->title = $faker->sentence(3);
            $article->body = $faker->paragraph(6);
            $article->tags =  join(',',$faker->words(4));
            $article->save();

            $article->addToIndex();
        }
    }
}

<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Main\Language;
use App\Models\Main\Type;
use App\Models\Main\Tag;
use App\Models\Main\Site;

class PublicSites extends Seeder
{

    protected $languages = [
        'Русский', 'English'
    ];

    protected $types = [
        'Landing page', 'Corporate Site', 'E-Market'
    ];

    protected $tags_count = 200;
    protected $sites_count = 50;


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $languages = new Collection();
        foreach($this->languages as $language) {
            $language = Language::create(['name' => $language]);
            $languages->push($language);
        }

        $types = new Collection();
        foreach($this->types as $type){
            $type = Type::create(['name' => $type]);
            $types->push($type);
        }

        $tags = new Collection();
        factory(Tag::class, $this->tags_count)->create()->each(function($tag) use ($tags) {
            $tags->push($tag);
        });

        factory(Site::class, $this->sites_count)->create()->each(function($site) use ($languages, $types, $tags) {

            // Добавление языков публичного сайта.
            $languages = $languages->random(rand(1, $languages->count()));
            foreach($languages as $language) {
                $site->languages()->attach($language->id);
            }

            // Добавление типов публичного сайта.
            $type = $types->random(1)->first();
            $site->type_id = $type->id;

            // Добавление тегов публичного сайта.
            $tags = $tags->random(rand(1, 30));
            foreach($tags as $tag) $site->tags()->attach($tag);

        });

    }

}

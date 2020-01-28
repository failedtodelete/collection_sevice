<?php

namespace App\Observers\Temp;

use App\Models\Temp\Link;
use App\Models\Temp\Site;
use App\Facades\Temp\TempSiteServiceFacade as TempSiteService;
use Illuminate\Support\Facades\Storage;

class SiteObserver
{
    /**
     * Handle the site "created" event.
     *
     * @param  \App\Models\Temp\Site  $site
     * @return void
     */
    public function created(Site $site)
    {

        // Обновление статуса ссылки.
        $site->link->update([
            'status' => Link::HOLDED
        ]);
    }

    /**
     * Обновление данных сайта.
     * Удаление изображений которых у сайта больше нет.
     * @param Site $site
     */
    public function updated(Site $site)
    {
        TempSiteService::delete_unused_images($site->id);
    }

    /**
     * Удаление сайта.
     * Удаление папки изображений текущего сайта.
     * @param Site $site
     */
    public function deleted(Site $site)
    {

        // Удаление всех изображений сайта.
        // Удаление папки {hash} из хранилища.
        Storage::disk('public')->deleteDirectory($site->hash);
    }

    /**
     * Handle the site "restored" event.
     *
     * @param  \App\Models\Temp\Site  $site
     * @return void
     */
    public function restored(Site $site)
    {
        //
    }

    /**
     * Handle the site "force deleted" event.
     *
     * @param  \App\Models\Temp\Site  $site
     * @return void
     */
    public function forceDeleted(Site $site)
    {
        //
    }
}

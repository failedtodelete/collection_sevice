<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cseed {type?} {--refresh} {--reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Custom functionality seed';

    /**
     * Определение направления установки seed's.
     * @var
     */
    protected $type;

    /**
     * Доступные типы запросов.
     * @var array
     */
    protected $available_types = ['admin', 'public', 'temp'];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // Опредедение входных параметров и аргументов.
        $this->type = strtolower($this->argument('type'));

        if (!mb_strlen($this->type)) {
            foreach($this->available_types as $type) {
                $this->implementation($type);
            }
            return false;
        }

        // Проверка передаваемого типа.
        if (!in_array($this->type, $this->available_types)) {
            $this->error('Type not recognized');
            return false;
        }

        $this->implementation($this->type);
    }

    /**
     * Выполнение запроса.
     * @param $type
     */
    protected function implementation($type)
    {
        $this->call("db:seed", ['--class' => ucfirst($type).'DatabaseSeeder']);
    }

    /**
     * Получение файлов указанного типа.
     * Получение названий всех файлов из директории.
     * @param $type
     * @return array
     */
    protected function getPathFiles($type) {

        // Конечный массив файлов.
        $result = [];

        // Получение директории сканирования.
        $path = "{$this->laravel->databasePath()}/seeds/{$type}";

        // Получение и перебор доступных файлов.
        $files = scandir($path);
        foreach($files as $file) {
            if ($file !== '.' && $file !== '..') {
               array_push($result, basename($file, ".php"));
            }
        }
        return $result;
    }

}

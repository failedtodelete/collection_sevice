<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cmigrate {type?} {--refresh} {--reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Custom functionality migrate';


    /**
     * Refresh method artisan migrate:{refresh}
     * Reset method artisan migrate:{reset}
     * @var bool
     */
    protected $refresh = false;
    protected $reset = false;

    /**
     * Определение направления миграций.
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
        $this->refresh = $this->option('refresh');
        $this->reset = $this->option('reset');

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
    public function implementation($type)
    {
        $this->info("Выполнение команды migrate для @{$type} типа");

        if ($this->reset) {
            $this->call('migrate:reset', ['--database' => "mysql_{$type}", '--path' => "database/migrations/{$type}"]);
        } else if ($this->refresh) {
            $this->call('migrate:refresh', ['--database' => "mysql_{$type}", '--path' => "database/migrations/{$type}"]);
        } else {
            $this->call('migrate', ['--database' => "mysql_{$type}", '--path' => "database/migrations/{$type}"]);
        }
    }

}

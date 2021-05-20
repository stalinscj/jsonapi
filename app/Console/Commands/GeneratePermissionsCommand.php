<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;

class GeneratePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate permissions for registered api resources';

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
     * @return int
     */
    public function handle()
    {
        $resources = config('json-api-v1.resources');

        foreach ($resources as $resource => $class) {

            $this->comment("Permissions for '$resource'");

            foreach (Permission::$abilities as $ability) {
                $name = "$resource:$ability";
                
                Permission::firstOrCreate(compact('name'));
                
                $this->line("$name");
            }
        }

        $this->info('Permissions generated!');

        return 0;
    }
}

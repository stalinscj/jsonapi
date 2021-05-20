<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class GenerateTestingData extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test data for the API.';

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
        if (! $this->confirmToProceed()) {
            return 1;
        }

        User::query()->delete();
        Article::query()->delete();
        Category::query()->delete();

        $user = User::factory()->hasArticles(1)->create([
            'name'  => 'Stalin',
            'email' => 'stalin@email.com'
        ]);

        $articles = Article::factory(14)->create();

        $this->info('User UUID:');
        $this->line($user->id);
        
        $this->info('Token:');
        $this->line($user->createToken('Stalin')->plainTextToken);
        
        $this->info('Article ID:');
        $this->line($user->articles->first()->slug);

        $this->info('Category ID:');
        $this->line($articles->first()->category->slug);

        return 0;
    }
}

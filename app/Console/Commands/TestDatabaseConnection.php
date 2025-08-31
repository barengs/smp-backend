<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDatabaseConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-database-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test database connection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Test the database connection
            DB::connection()->getPdo();
            $this->info('Database connection successful!');

            // List all tables
            $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
            $this->info('Tables in database:');
            foreach ($tables as $table) {
                $this->line('- ' . $table->table_name);
            }
        } catch (\Exception $e) {
            $this->error('Database connection failed: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Event;
use App\Models\Destination;
use App\Models\Story;
use App\Models\Activation;
use App\Models\Category;

class SafeSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:safe-seed {--class=DatabaseSeeder} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safe database seeding with confirmation in production';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $class = $this->option('class');
        
        // Check if production environment
        if (app()->environment('production') && !$this->option('force')) {
            $this->error('⚠️  WARNING: You are about to seed the PRODUCTION database!');
            $this->warn('This may overwrite existing data.');
            $this->newLine();
            
            // Show current record counts
            $this->info('Current database state:');
            $this->table(
                ['Table', 'Record Count'],
                [
                    ['users', User::count()],
                    ['events', Event::count()],
                    ['destinations', Destination::count()],
                    ['stories', Story::count()],
                    ['activations', Activation::count()],
                    ['categories', Category::count()],
                ]
            );
            
            $this->newLine();
            
            if (!$this->confirm('Are you ABSOLUTELY SURE you want to continue?')) {
                $this->info('✅ Seeding cancelled. Your data is safe.');
                return Command::SUCCESS;
            }
            
            $confirmation = $this->ask('Type "YES" (all caps) to confirm');
            if ($confirmation !== 'YES') {
                $this->info('✅ Seeding cancelled. Your data is safe.');
                return Command::SUCCESS;
            }
            
            $this->newLine();
            $this->warn('Creating backup before seeding...');
            
            // Trigger backup (if backup script exists)
            if (file_exists('/home/deploy/backup-mysql.sh')) {
                exec('/home/deploy/backup-mysql.sh', $output, $return);
                if ($return === 0) {
                    $this->info('✅ Backup created successfully');
                } else {
                    $this->error('❌ Backup failed!');
                    if (!$this->confirm('Continue seeding without backup? (NOT RECOMMENDED)')) {
                        $this->info('✅ Seeding cancelled. Your data is safe.');
                        return Command::SUCCESS;
                    }
                }
            } else {
                $this->warn('Backup script not found at /home/deploy/backup-mysql.sh');
            }
        }
        
        $this->info("Running seeder: {$class}");
        $this->newLine();
        
        Artisan::call('db:seed', [
            '--class' => $class,
            '--force' => true
        ]);
        
        $this->info(Artisan::output());
        $this->newLine();
        $this->info('✅ Seeding completed.');
        
        return Command::SUCCESS;
    }
}

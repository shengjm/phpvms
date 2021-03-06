<?php

namespace App\Console\Commands;

use DB;
use PDO;

use App\Models\Airline;
use App\Models\User;

use App\Console\BaseCommand;
use App\Models\Acars;
use App\Models\Pirep;

class DevCommands extends BaseCommand
{
    protected $signature = 'phpvms {cmd}';
    protected $description = 'Developer commands';

    /**
     * Run dev related commands
     */
    public function handle()
    {
        $command = trim($this->argument('cmd'));

        if (!$command) {
            $this->error('No command specified!');
            exit();
        }

        $commands = [
            'clear-acars' => 'clearAcars',
            'clear-users' => 'clearUsers',
            'compile-assets' => 'compileAssets',
            'db-attrs' => 'dbAttrs',
        ];

        if(!array_key_exists($command, $commands)) {
            $this->error('Command not found!');
            exit();
        }

        $this->{$commands[$command]}();
    }

    /**
     * Delete all the data from the ACARS and PIREP tables
     */
    protected function clearAcars()
    {
        if(config('database.default') === 'mysql') {
            DB::statement('SET foreign_key_checks=0');
        }

        Acars::truncate();
        Pirep::truncate();

        if (config('database.default') === 'mysql') {
            DB::statement('SET foreign_key_checks=1');
        }

        $this->info('ACARS and PIREPs cleared!');
    }

    /**
     * Delete all the data from the ACARS and PIREP tables
     */
    protected function clearUsers()
    {
        if (config('database.default') === 'mysql') {
            DB::statement('SET foreign_key_checks=0');
        }

        DB::statement('TRUNCATE `role_user`');
        Airline::truncate();
        User::truncate();

        if (config('database.default') === 'mysql') {
            DB::statement('SET foreign_key_checks=1');
        }

        $this->info('Users cleared!');
    }

    /**
     * Compile all the CSS/JS assets into their respective files
     * Calling the webpack compiler
     */
    protected function compileAssets()
    {
        $this->runCommand('npm update');
        $this->runCommand('npm run dev');
    }

    /**
     * Output DB prepares versions
     */
    protected function dbAttrs()
    {
        $pdo = DB::connection()->getPdo();
        $emulate_prepares_below_version = '5.1.17';
        $server_version = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
        $emulate_prepares = version_compare($server_version, $emulate_prepares_below_version, '<');

        $this->info('Server Version: '. $server_version);
        $this->info('Emulate Prepares: '.$emulate_prepares);

        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, $emulate_prepares);
    }
}

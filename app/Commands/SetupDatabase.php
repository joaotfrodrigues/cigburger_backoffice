<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SetupDatabase extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Setup';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'setup:database';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Run all the migrations and seeders';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'setup:database';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        // Run the migrations
        $this->call('migrate');

        // Array of seed files
        $seeds = [
            'RestaurantsSeeder',
            'UsersSeeder',
            'ProductsSeeder'
        ];        

        // Loop through each seed and run it
        foreach ($seeds as $seed) {
            $this->call('db:seed', [$seed]);
        }

        CLI::write('The database setup was successful.');
    }
}

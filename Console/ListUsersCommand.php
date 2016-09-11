<?php

namespace Modules\System\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\System\Repositories\UserRepository;

class ListUsersCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'six:list users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all unix users (except system users).';

    /**
     * The repository
     *
     * @var UserRepository
     */
    protected $users;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserRepository $users)
    {
        parent::__construct();

        $this->users = $users;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $users = $this->users->get();

        $this->table(['Username', 'Status', 'Home'], $users);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            
        ];
    }
}

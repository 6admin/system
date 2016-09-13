<?php

namespace Services\System\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Services\System\Repositories\UserRepository;

class CreateUserCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'six:create user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new unix user.';

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
        $this->users->create(
            array_only(
                array_merge($this->arguments(), $this->options()), 
                ["name", "password", "home", "group"]
            )
        );
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the unix account.'],
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
            ['password', 'p', InputOption::VALUE_OPTIONAL, 'The password of the account (leave blank for no cli access on secure installations).', null],
            ['home', null, InputOption::VALUE_OPTIONAL, 'Create and assign a home directory to this user.', null],
            ['group', null, InputOption::VALUE_OPTIONAL, 'Assign a specific group for this user.', null],
        ];
    }
}

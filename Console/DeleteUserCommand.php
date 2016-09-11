<?php

namespace Modules\System\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Modules\System\Repositories\UserRepository;

class DeleteUserCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'six:delete user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete the unix user.';

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
        $this->users->delete($this->argument('name'), [
            'files' => $this->option('files')
        ]);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the unix account to delete.'],
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
            ['files', null, InputOption::VALUE_NONE, 'Delete the home folder and the mail pool of the user.', null],
        ];
    }
}

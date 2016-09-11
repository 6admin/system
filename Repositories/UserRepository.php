<?php
namespace Modules\System\Repositories;

use App\Contracts\Repository;
use App\Repository\HasInputValidation;
use App\System\Command as SystemCommand;

class UserRepository implements Repository
{
    use HasInputValidation;

    public $validation = [
        'name' => 'required|alpha_dash',
        'password' => 'string|nullable',
        'home' => 'string|nullable',
        'group' => 'string|nullable',
    ];

    /**
     * Get an Item based on his $id or get all Items if $id is null.
     *
     * @param  mixed  $id
     * @param  array  $options
     * @return Item|array
     */
    public function get($id = null, array $options = [])
    {
        // TODO : Bad implementation here, think about refactoring with User Item and methods

        $users = [];
        $userStatus = [];

        foreach(explode("\n", file_get_contents('/etc/shadow')) as $line) {
            if(!empty($line)) {
                list($username, $password, $days) = explode(':', $line, 3);

                if($password == '*' OR $password == '!') {
                    $userStatus[$username] = 'Inactive';
                }
                else {
                    $userStatus[$username] = 'Active';
                }
            }
        }

        foreach(explode("\n", file_get_contents('/etc/passwd')) as $line) {
            if(!empty($line)) {
                list($username, $password, $uid, $gid, $realname, $home, $shell) = explode(':', $line);

                if($uid < 1000 OR $uid == 65534)
                    continue;

                $users[$username] = [
                    'username' => $username,
                    'status' => $userStatus[$username]
                ];

                if(file_exists($home)) {
                    $users[$username]['home'] = $home;
                }
            }
        }

        return $users;
    }

    /**
     * Create an new Item with the given $datas. Then return the created
     * Item.
     *
     * @param  array  $datas
     * @param  array  $options
     * @return Item
     */
    public function create(array $datas = [], array $options = [])
    {
        if($this->validate($datas)) {
            $args = [$datas['name']];
            $opts = array_filter(array_except($datas, ['name']));

            // Handle home folder creation
            if(isset($opts['home'])) {
                $opts['create-home'] = '';
            }

            // Handle for password encryption, replace password by its hash
            if(isset($opts['password'])) {
                $hash = SystemCommand::run('/usr/bin/mkpasswd', $opts['password'], ['-H' => 'md5']);

                if(!isset($hash[0]) OR empty($hash[0])) {
                    throw new \Modules\System\Exceptions\UserPasswordEncryptionException("Invalid hash returned by mkpasswd : " . var_export($hash, true));
                }

                $opts['password'] = $hash[0];
            }

            SystemCommand::run('/usr/sbin/useradd', $args, $opts);
        }
    }

    /**
     * Update an Item by his his $id and set the $datas into it. Then
     * return the Item after modifications.
     *
     * @param  mixed  $id
     * @param  array  $datas
     * @param  array  $options
     * @return Item
     */
    public function update($id, array $datas, array $options = [])
    {
        // TODO : use usermod for changing username and home dir
        // TODO : use passwd for changing password of the user
    }

    /**
     * Delete an Item by his his $id.
     *
     * @param  mixed  $id
     * @param  array  $options
     */
    public function delete($id, array $options = [])
    {
        $args = [$id];
        $opts = [];

        if(isset($options['files']) AND $options['files']) {
            $opts['remove'] = true;
        }

        SystemCommand::run('/usr/sbin/userdel', $id, $opts);
    }
}
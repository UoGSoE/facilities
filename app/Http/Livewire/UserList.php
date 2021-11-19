<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Ohffs\Ldap\LdapConnectionInterface;

class UserList extends Component
{
    public $removals = [];

    public $guid = '';
    public $surname = '';
    public $forenames = '';
    public $email = '';

    public $errorMessage = '';

    protected $rules = [
        'guid' => 'required|string|max:255',
        'surname' => 'required|string|max:255',
        'forenames' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
    ];

    public function render()
    {
        return view('livewire.user-list', [
            'users' => User::orderBy('surname')->get(),
        ]);
    }

    public function remove()
    {
        if (count($this->removals) == 0) {
            return;
        }

        collect($this->removals)
            ->filter(fn ($userId) => $userId != Auth::id())
            ->each(fn ($userId) => User::find($userId)->delete());

        $this->removals = [];
    }

    public function updatingGuid()
    {
        $this->errorMessage = '';
    }

    public function search()
    {
        $this->email = '';
        $this->surname = '';
        $this->forenames = '';

        if (! $this->guid) {
            return;
        }

        try {
            $user = \Ldap::findUser($this->guid);
            if (! $user) {
                $this->errorMessage = 'Cannot find that GUID';
                return;
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Cannot connect to University LDAP server';
            return;
        }

        $this->email = $user->email;
        $this->surname = $user->surname;
        $this->forenames = $user->forenames;
    }

    public function add()
    {
        $this->validate();

        $user = User::firstOrNew(['username' => $this->guid]);
        $user->username = $this->guid;
        $user->surname = $this->surname;
        $user->forenames = $this->forenames;
        $user->email = $this->email;
        $user->password = bcrypt(Str::random(64));
        $user->is_staff = true;
        $user->is_admin = true;
        $user->save();

        $this->reset();
    }
}

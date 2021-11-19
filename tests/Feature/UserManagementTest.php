<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use Ohffs\Ldap\LdapUser;
use App\Http\Livewire\UserList;
use Ohffs\Ldap\FakeLdapConnection;
use Ohffs\Ldap\LdapConnectionInterface;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_user_admin_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.index'));

        $response->assertOk();
        $response->assertSee('Admin Users');
        $response->assertSeeLivewire('user-list');
    }

    /** @test */
    public function we_can_remove_an_existing_admin()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Livewire::actingAs($user)->test(UserList::class)
            ->assertSee($user->full_name)
            ->assertSee($otherUser->full_name)
            ->set('removals', [$otherUser->id])
            ->call('remove')
            ->assertDontSee($otherUser->full_name)
            ->assertSee($user->full_name);

        $this->assertDatabaseMissing('users', ['id' => $otherUser->id]);
    }

    /** @test */
    public function admins_cant_remove_themselves()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Livewire::actingAs($user)->test(UserList::class)
            ->assertSee($user->full_name)
            ->assertSee($otherUser->full_name)
            ->set('removals', [$user->id])
            ->call('remove')
            ->assertSee($otherUser->full_name)
            ->assertSee($user->full_name);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /** @test */
    public function we_can_add_a_new_user()
    {
        $this->fakeLdapConnection();
        \Ldap::shouldReceive('findUser')->with('test1x')->andReturn(new LdapUser([
            [
            'uid' => ['test1x'],
            'mail' => ['testy@example.com'],
            'sn' => ['mctesty'],
            'givenname' => ['testy'],
            'telephonenumber' => ['12345'],
            ],
        ]));
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(UserList::class)
            ->set('guid', 'test1x')
            ->call('search')
            ->assertSet('forenames', 'testy')
            ->assertSet('surname', 'mctesty')
            ->assertSet('email', 'testy@example.com')
            ->call('add');
    }

    /** @test */
    public function trying_to_add_a_non_existent_user_returns_an_error()
    {
        $this->fakeLdapConnection();
        \Ldap::shouldReceive('findUser')->with('test1x')->andReturn(false);
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(UserList::class)
            ->set('guid', 'test1x')
            ->call('search')
            ->assertSee('Cannot find that GUID')
            ->assertSet('forenames', '')
            ->assertDontSee('addNewUserButton');
    }


    private function fakeLdapConnection()
    {
        $this->instance(
            LdapConnectionInterface::class,
            new FakeLdapConnection('up', 'whatever')
        );
    }
}

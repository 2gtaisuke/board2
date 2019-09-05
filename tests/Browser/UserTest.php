<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends DuskTestCase
{

    use DatabaseMigrations;

    /** @var User */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function ログインしていない場合にヘッダーに『SignUp』『Login』の文字列が表示されること()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Sign up')
                ->assertSee('Login');
        });
    }

    /**
     * @test
     */
    public function ログインしている場合にヘッダーにユーザーのプロフィール画像が表示されること()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/')
                ->assertSourceHas(get_user_profile_image($this->user->image_path));
        });
    }
}
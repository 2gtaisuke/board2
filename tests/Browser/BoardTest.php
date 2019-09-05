<?php

namespace Tests\Browser;

use App\Models\Board;
use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BoardTest extends DuskTestCase
{
    use DatabaseMigrations;

    private $login_user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->login_user = factory(User::class)->create();
    }

    /**
     * @test
     * @throws \Throwable
     */
    public function 掲示版作成()
    {
        $title = 'this is dusk test board[title]';
        $content = 'this is dusk comment[content]';
        $this->browse(function (Browser $browser) use ($title, $content) {
            $browser->loginAs($this->login_user)
                ->visit(route('board.create', [], false))
                ->type('board[title]', $title)
                ->type('comment[content]', $content)
                ->press('作成する')
                ->assertPathIs(route('board.index', [], false))
                ->assertSee($title)
                ->assertSee($content);
        });
    }

    /**
     * @test
     * @throws \Throwable
     */
    public function 掲示版作成_必須項目未入力時にはinvalidが表示されること()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->login_user)
                ->visit(route('board.create', [], false))
                ->press('作成する')
                ->assertPathIs(route('board.create', [], false))
                ->assertSourceHas('is-invalid');
        });
    }

    /**
     *@test
     */
    public function コメント投稿()
    {
        $board = factory(User::class)->create()->boards()->save(factory(Board::class)->make());
        $content = 'this is dusk comment[content]';

        $this->browse(function (Browser $browser) use ($board, $content) {
            $browser->loginAs($this->login_user)
                ->visit(route('board.show', ['id' => $board->id], false))
                ->type('comment[content]', $content)
                ->press('投稿する')
                ->assertPathIs(route('board.show', ['id' => $board->id], false))
                ->assertSee($content);
        });
    }

    /**
     * @test
     */
    public function コメント投稿_必須項目未入力時にはinvalidが表示されること()
    {
        $board = factory(User::class)->create()->boards()->save(factory(Board::class)->make());

        $this->browse(function (Browser $browser) use ($board) {
            $browser->loginAs($this->login_user)
                ->visit(route('board.show', ['id' => $board->id], false))
                ->press('投稿する')
                ->assertPathIs(route('board.show', ['id' => $board->id], false))
                ->assertSourceHas('is-invalid');
        });
    }
}
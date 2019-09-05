<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\User;
use App\Service\UserService;
use Tests\TestCase;

class BoardTest extends TestCase
{
    /** @var User */
    private $login_user;

    /** @var UserService */
    private $user_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->login_user = factory(User::class)->create();
        $this->user_service = app()->make(UserService::class);
    }

    /**
     * @test
     */
    public function store()
    {
        $title = 'test title';
        $content = 'test comment';

        $this->actingAs($this->login_user);

        $this->get(route('board.create'));

        $response = $this->post(
            route('board.store'),
            [
                'board' => [
                    'title' => $title
                ],
                'comment' => [
                    'content' => $content
                ],
                'tags' => 'foo bar'
            ]
        );

        $response->assertStatus(302);
        $response->assertLocation(route('board.index'));
        $this->assertDatabaseHas(
            'boards',
            ['title' => $title]
        );
        $this->assertDatabaseHas(
            'comments',
            ['content' => $content]
        );
        $this->assertDatabaseHas(
            'tags',
            ['name' => 'foo']
        );
    }

    /**
     * @test
     */
    public function store_ログインしていなければ掲示版を作成できないこと()
    {
        $title = 'test title';
        $content = 'test content';

        $this->get(route('board.create'));
        $response = $this->post(
            route('board.store'),
            [
                'board' => [
                    'title' => $title
                ],
                'comment' => [
                    'content' => $content
                ],
            ]
        );

        $response->assertStatus(302);
        $response->assertLocation(route('login'));
        $this->assertDatabaseMissing(
            'boards',
            ['title' => $title]
        );
        $this->assertDatabaseMissing(
            'comments',
            ['content' => $content]
        );
    }

    /**
     * @test
     */
    public function storeComment()
    {
        $content = 'test content';

        $board_owned_user = factory(User::class)->create();
        $board = $board_owned_user->boards()->save(factory(Board::class)->make());

        $this->actingAs($this->login_user);
        $this->get(route('board.show', ['id' => $board->id]));

        $response = $this->post(
            route('comment.store', ['id' => $board->id]),
            [
                'comment' => [
                    'content' => $content
                ]
            ]
        );

        $response->assertStatus(302);
        $response->assertLocation(route('board.show', ['id' => $board->id]));
        $this->assertDatabaseHas(
            'comments',
            ['content' => $content]
        );
    }

    /**
     * @test
     */
    public function storeCommet_ログインしていなければコメントを投稿できないこと()
    {
        $content = 'test content';
        $board_owned_user = factory(User::class)->create();
        $board = $board_owned_user->boards()->save(factory(Board::class)->make());

        $this->get(route('board.show', ['id' => $board->id]));

        $response = $this->post(
            route('comment.store', ['id' => $board->id]),
            [
                'comment' => [
                    'content' => $content
                ]
            ]
        );

        $response->assertStatus(302);
        $response->assertLocation(route('login'));
        $this->assertDatabaseMissing(
            'comments',
            ['content' => $content]
        );
    }

    /**
     * @test
     * @group feature-like
     */
    public function like()
    {
        /** @var User $board_owned_user */
        $board_owned_user = factory(User::class)->create();
        /** @var Board $board */
        $board = $board_owned_user->boards()->save(factory(Board::class)->make());

        $this
            ->actingAs($board_owned_user, 'api')
            ->post(
                route('board.like', ['id' => $board->id])
            )
            ->assertStatus(200);

        $this->assertTrue($board_owned_user->isLike($board));
    }

    /**
     * @test
     * @group feature-like
     */
    public function like_認証していない場合にステータスコードが302であること()
    {
        /** @var User $board_owned_user */
        $board_owned_user = factory(User::class)->create();
        /** @var Board $board */
        $board = $board_owned_user->boards()->save(factory(Board::class)->make());

        $this
            ->post(
                route('board.like', ['id' => $board->id])
            )
            ->assertStatus(302);
    }

    /**
     * @test
     * @group feature-like
     */
    public function like_既に『いいね』済みの場合に409を返し、『いいね』が外れること()
    {
        /** @var User $board_owned_user */
        $board_owned_user = factory(User::class)->create();
        /** @var Board $board */
        $board = $board_owned_user->boards()->save(factory(Board::class)->make());
        $board_owned_user->like($board);

        $this
            ->actingAs($board_owned_user, 'api')
            ->post(
                route('board.like', ['id' => $board->id])
            )
            ->assertStatus(200);

        $this->assertFalse($board_owned_user->isLike($board));
    }
}
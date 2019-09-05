<?php

namespace Tests\Unit;

use App\Models\Board;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\User;
use App\Service\BoardService;
use Illuminate\Pagination\Paginator;
use Tests\TestCase;

class BoardServiceTest extends TestCase
{
    /** @var BoardService */
    private $board_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->board_service = app()->make(BoardService::class);
    }

    /**
     * @test
     */
    public function find()
    {
        factory(User::class)->create()->each(function($user) {
            $user->boards()->save(factory(Board::class)->make());
        });
        $board = Board::first();

        $fetched_board = $this->board_service->find($board->id);

        $this->assertEquals($board->id, $fetched_board->id);
    }

    /**
     * @test
     */
    public function getWithPaginationOfComments()
    {
        // BoardとComment作成
        factory(User::class)->create()->each(function ($user) {
            $board = factory(Board::class)->create([
                'user_id' => $user->id
            ]);
            factory(Comment::class)->create([
                'board_id' => $board->id,
                'user_id' => $user->id
            ]);
        });
        $board = Board::first();

        $board_with_comments = $this->board_service->getWithPaginationOfComments($board->id);

        $this->assertInstanceOf(Paginator::class, $board_with_comments->comments);
    }

    /**
     * @test
     * @group store
     */
    public function store()
    {
        $user = factory(User::class)->create();
        $title = 'test title';
        $content = 'test content';
        $tags = 'foo bar';

        $this->board_service->store($user->id, $title, $content, $tags);

        $created_board = Board::first();
        $created_comment = Comment::first();

        $this->assertEquals($title, $created_board->title);
        $this->assertEquals($content, $created_comment->content);
        $created_board->tags->contains('name', $tags[0]);
        $created_board->tags->contains('name', $tags[1]);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage ユーザーが存在しません
     */
    public function store_ユーザーが存在しない場合に例外をスローすること()
    {
        $title = 'test title';
        $content = 'test content';
        $tags = 'foo bar';
        $this->board_service->store(1, $title, $content, $tags);
    }

    /**
     * @test
     */
    public function storeComment()
    {
        $user = factory(User::class)->create();
        $board = $user->boards()->save(factory(Board::class)->make());
        $content = 'test content';
        $this->board_service->storeComment($user->id, $board->id, $content);

        $this->assertEquals($content, $board->comments()->first()->content);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage ユーザーが存在しません
     */
    public function storeComment_ユーザーが存在しない場合に例外をスローすること()
    {
        $user = factory(User::class)->create();
        $board = $user->boards()->save(factory(Board::class)->make());
        $content = 'test content';
        $this->board_service->storeComment(1000, $board->id, $content);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage 掲示版が存在しません
     */
    public function storeComment_掲示版が存在しない場合に例外をスローすること()
    {
        $user = factory(User::class)->create();
        $content = 'test content';
        $this->board_service->storeComment($user->id, 1000, $content);
    }

    /**
     * @test
     * @group retrieveTags
     */
    public function retrieveTags()
    {
        $user = factory(User::class)->create();
        $board = factory(Board::class)->make();
        $user->boards()->save($board);
        $tag = factory(Tag::class)->create();
        $board->tags()->attach($tag);

        $tags = $this->board_service->retrieveTags($board->id);

        $this->assertCount(1, $tags);
    }

}
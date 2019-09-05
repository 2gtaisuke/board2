<?php

namespace Tests\Unit;

use App\Models\Board;
use App\Models\User;
use Tests\TestCase;

class LikeTest extends TestCase
{
    /** @var User */
    private $user;

    /** @var Board */
    private $board;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->user->boards()->save(factory(Board::class)->make());
        $this->board = $this->user->load('boards')->boards->first();
    }

    /**
     * @test
     * @group like
     */
    public function like()
    {
        $this->user->like($this->board);

        $this->assertDatabaseHas(
            'likes',
            [
                'user_id'  => $this->user->id,
                'board_id' => $this->board->id
            ]
        );
    }

    /**
     * @test
     * @group like
     * @depends like
     * @expectedException \Exception
     */
    public function like_既に『いいね』している場合は例外をスローする()
    {
        $this->user->like($this->board);
        $this->user->like($this->board);
    }

    /**
     * @test
     * @group offLike
     * @depends like_既に『いいね』している場合は例外をスローする
     */
    public function offLike()
    {
        $this->user->like($this->board);
        $this->user->offLike($this->board);

        $this->assertDatabaseMissing(
            'likes',
            [
                'user_id'  => $this->user->id,
                'board_id' => $this->board->id,
            ]
        );
    }

    /**
     * @test
     * @group offLike
     * @depends offLike
     * @expectedException \Exception
     */
    public function offLike_まだ『いいね』していない場合は例外をスローすること()
    {
        $this->user->offLike($this->board);
    }

    /**
     * @test
     * @group isLike
     * @depends like_既に『いいね』している場合は例外をスローする
     */
    public function isLike_『いいね』をしている場合はtrueを返すこと()
    {
        $this->user->like($this->board);
        $this->assertTrue($this->user->isLike($this->board));
    }

    /**
     * @test
     * @group isLike
     * @depends isLike_『いいね』をしている場合はtrueを返すこと
     */
    public function isLike_『いいね』していない場合はfalseを返すこと()
    {
        $this->assertFalse($this->user->isLike($this->board));
    }
}
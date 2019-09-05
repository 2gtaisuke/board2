<?php

namespace Tests\Unit;

use App\Exceptions\FollowUserException;
use App\Exceptions\LikeUserException;
use App\Models\Board;
use App\Models\User;
use App\Service\UserService;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    /** @var UserService */
    private $user_service;

    protected function setUp(): void
    {
        parent::setUp();

        factory(User::class, 4)->create();
        $this->user_service = app()->make(UserService::class);
    }

    /**
     * @test
     * @group followUser
     */
    public function followUser()
    {
        $user = User::first();
        $followed_user = User::find(2);
        $this->user_service->followUser($user, $followed_user);

        $this->assertEquals($user->following()->first()->id, $followed_user->id);
    }

    /**
     * @test
     * @group followUser
     * @expectedException App\Exceptions\FollowUserException
     */
    public function followUser_既にフォローしてる場合に例外をスローすること()
    {
        $user = User::first();
        $followed_user = User::find(2);
        $this->user_service->followUser($user, $followed_user);
        $this->user_service->followUser($user, $followed_user);
    }

    /**
     * @test
     * @group followUser
     * @expectedException App\Exceptions\FollowUserException
     */
    public function followUser_自分自身の場合に例外をスローすること()
    {
        $user = $followed_user = User::first();
        $this->user_service->followUser($user, $followed_user);
    }

    /**
     * @test
     * @group unfollowUser
     */
    public function unfollowUser()
    {
        $user = User::first();
        $followed_user = User::find(2);
        $user->following()->attach($followed_user->id);

        $this->user_service->unfollowUser($user, $followed_user);

        $this->assertFalse($user->isFollowing($followed_user));
    }

    /**
     * @test
     * @group unfollowUser
     * @expectedException App\Exceptions\FollowUserException
     */
    public function unfollowUser_まだフォローしていない場合に例外をスローすること()
    {
        $user = User::first();
        $followed_user = User::find(2);

        $this->user_service->unfollowUser($user, $followed_user);
    }

    /**
     * @test
     * @group unfollowUser
     * @expectedException App\Exceptions\FollowUserException
     */
    public function unfollowUser_自分自身の場合に例外をスローすること()
    {
        $user = $followed_user = User::first();
        $this->user_service->unfollowUser($user, $followed_user);
    }

    /**
     * @test
     * @group likeBoard
     */
    public function likeBoard()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();

        $this->user_service->likeBoard($user->id, $board->id);

        $this->assertTrue($user->isLike($board));
    }

    /**
     * @test
     * @group likeBoard
     * @expectedException \Exception
     * @expectedExceptionMessage ユーザーが存在しません
     */
    public function likeBoard_Userが存在しない場合に例外をスローすること()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();
        $not_found_user_id = 1000;

        $this->user_service->likeBoard($not_found_user_id, $board->id);
    }

    /**
     * @test
     * @group likeBoard
     * @expectedException \Exception
     * @expectedExceptionMessage 掲示版が存在しません
     */
    public function likeBoard_Boardが存在しない場合に例外をスローすること()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();
        $not_found_board_id = 1000;

        $this->user_service->likeBoard($user->id, $not_found_board_id);
    }

    /**
     * @test
     * @group likeBoard
     * @expectedException \Exception
     * @expectedExceptionMessage ユーザーは既に掲示版を『いいね』しています
     */
    public function likeBoard_UserがBoardを既に『いいね』している場合に例外をスローすること()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();

        $this->user_service->likeBoard($user->id, $board->id);
        $this->user_service->likeBoard($user->id, $board->id);
    }

    /**
     * @test
     * @group offLikeBoard
     * @depends likeBoard
     */
    public function offLikeBoard()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();

        $this->user_service->likeBoard($user->id, $board->id);
        $this->user_service->offLikeBoard($user->id, $board->id);

        $this->assertFalse($user->isLike($board));
    }

    /**
     * @test
     * @group offLikeBoard
     * @expectedException \Exception
     * @expectedExceptionMessage ユーザーが存在しません
     */
    public function offLikeBoard_Userが存在しない場合に例外をスローすること()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();
        $not_found_user_id = 1000;

        $this->user_service->offLikeBoard($not_found_user_id, $board->id);
    }

    /**
     * @test
     * @group offLikeBoard
     * @expectedException \Exception
     * @expectedExceptionMessage 掲示版が存在しません
     */
    public function offLikeBoard_Boardが存在しない場合に例外をスローすること()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();
        $not_found_board_id = 1000;

        $this->user_service->offLikeBoard($user->id, $not_found_board_id);
    }

    /**
     * @test
     * @group offLikeBoard
     * @expectedException \Exception
     * @expectedExceptionMessage ユーザーはまだ掲示版を『いいね』していません
     */
    public function offLikeBoard_UserがBoardをまだ『いいね』していない場合に例外をスローすること()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();

        $this->user_service->offLikeBoard($user->id, $board->id);
    }


    /**
     * @test
     */
    public function isLikeBoard_既にUserがBoardを『いいね』している場合にtrueを返すこと()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();

        $user->like($board);

        $this->assertTrue($this->user_service->isLikeBoard($user->id, $board->id));
    }

    /**
     * @test
     */
    public function isLikeBoard_UserがBoardをまだ『いいね』していない場合にfalseを返すこと()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();

        $this->assertFalse($this->user_service->isLikeBoard($user->id, $board->id));
    }

    /**
     * @test
     * @expectedException App\Exceptions\LikeUserException ユーザーが存在しません
     */
    public function isLikeBoard_Userが存在しない場合に例外をスローすること()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();
        $not_found_user_id = 1000;

        $this->assertFalse($this->user_service->isLikeBoard($not_found_user_id, $board->id));
    }

    /**
     * @test
     * @expectedException App\Exceptions\LikeUserException 掲示版が存在しません
     */
    public function isLikeBoard_Boardが存在しない場合に例外をスローすること()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();
        $not_found_board_id = 1000;

        $this->assertFalse($this->user_service->isLikeBoard($user->id, $not_found_board_id));
    }

    /**
     * @test
     * @group toggleLikeBoard
     */
    public function toggleLikeBoard_まだ『いいね』されてない場合に『いいね』すること()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();

        $this->user_service->toggleLikeBoard($user->id, $board->id);

        $this->assertTrue($user->isLike($board));
    }

    /**
     * @test
     * @depends toggleLikeBoard_まだ『いいね』されてない場合に『いいね』すること
     * @group toggleLikeBoard
     */
    public function toggleLikeBoard_既に『いいね』されている場合に『いいね』を外すこと()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();

        $this->user_service->toggleLikeBoard($user->id, $board->id);
        $this->user_service->toggleLikeBoard($user->id, $board->id);

        $this->assertFalse($user->isLike($board));

    }

    /**
     * @test
     * @group toggleLikeBoard
     * @expectedException App\Exceptions\LikeUserException
     * @expectedExceptionMessage ユーザーが存在しません
     */
    public function toggleLikeBoard_Userが存在しない場合に例外をスローすること()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();
        $not_found_user_id = 1000;

        $this->user_service->toggleLikeBoard($not_found_user_id, $board->id);
    }

    /**
     * @test
     * @group toggleLikeBoard
     * @expectedException App\Exceptions\LikeUserException
     * @expectedExceptionMessage 掲示版が存在しません
     */
    public function toggleLikeBoard_Boardが存在しない場合に例外をスローすること()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();
        $not_found_board_id = 1000;

        $this->user_service->toggleLikeBoard($user->id, $not_found_board_id);
    }

    /**
     * @test
     * @group retrievelikes
     */
    public function retrieveLikes()
    {
        $user = factory(User::class)->create();
        $user->boards()->save(factory(Board::class)->make());
        $board = $user->boards()->get()->first();
        $this->user_service->likeBoard($user->id, $board->id);

        $likes = $this->user_service->retrieveLikes($user->id);
        $this->assertCount(1, $likes);
    }

    /**
     * @test
     * @group getUserProfileImage
     */
    public function getUserProfileImage_プロフィール画像が存在する場合にそのパスを返すこと()
    {
        /** @var User $user */
        $user = factory(User::class)->create(['image_path' => 'foobar.jpg']);

        $user_profile_path = $this->user_service->getUserProfileImage($user);

        $this->assertRegExp("/{$user->image_path}/", $user_profile_path);
    }

    /**
     * @test
     * @group getUserProfileImage
     */
    public function getUserProfileImage_プロフィール画像が存在しない場合にunknown_userを返すこと()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $user_profile_path = $this->user_service->getUserProfileImage($user);

        $this->assertRegExp("/unknown_user.jpeg/", $user_profile_path);
    }
}

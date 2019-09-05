<?php

namespace Tests\Unit;

use App\Models\User;
use http\Exception;
use Tests\TestCase;

class FollowTest extends TestCase
{
    /** @var User */
    private $user;

    /** @var User */
    private $followed_user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->followed_user = factory(User::class)->create();
    }

    /**
     * @test
     * @group follow
     */
    public function follow()
    {
        $this->user->follow($this->followed_user);

        $this->assertDatabaseHas(
            'follow_users',
            [
                'user_id' => $this->user->id,
                'followed_user_id' => $this->followed_user->id
            ]
        );
    }

    /**
     * @test
     * @group follow
     * @depends follow
     * @expectedException \Exception
     */
    public function follow_既にフォローしていれば例外をスローすること()
    {
        $this->user->follow($this->followed_user);
        $this->user->follow($this->followed_user);
    }

    /**
     * @test
     * @group unfollow
     * @depends follow_既にフォローしていれば例外をスローすること
     */
    public function unfollow()
    {
        $this->user->follow($this->followed_user);
        $this->user->unfollow($this->followed_user);

        $this->assertDatabaseMissing(
            'follow_users',
            [
                'user_id' => $this->user->id,
                'followed_user_id' => $this->followed_user->id
            ]
        );
    }

    /**
     * @test
     * @group unfollow
     * @depends unfollow
     * @expectedException \Exception
     */
    public function unfollow_まだフォローしていなければ例外をスローすること()
    {
        $this->user->unfollow($this->followed_user);
    }

    /**
     * @test
     * @group isFollowing
     * @depends follow_既にフォローしていれば例外をスローすること
     */
    public function isFollowing_フォローしていればtrueを返すこと()
    {
        $this->user->follow($this->followed_user);

        $this->assertTrue($this->user->isFollowing($this->followed_user));
    }

    /**
     * @test
     * @group isFollowing
     * @depends isFollowing_フォローしていればtrueを返すこと
     */
    public function isFollowing_フォローしていなければfalseを返すこと()
    {
        $this->assertFalse($this->user->isFollowing($this->followed_user));
    }
}
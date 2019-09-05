<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class FollowUserTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        factory(User::class, 5)->create();
    }

    /**
     * @test
     */
    public function followUser_ログインしている場合にユーザーをフォローできること()
    {
        $following_user = User::first();
        $followed_user  = User::find(2);

        $this->actingAs($following_user);

        $this->get(route('user.show', ['id' => $followed_user->id], false));

        $response = $this->post(route('user.follow', ['id' => $followed_user->id], false));
        $response->assertStatus(302);
        $response->assertLocation(route('user.show', ['id' => $followed_user->id], false));

        $this->assertTrue($following_user->isFollowing($followed_user));
    }

    /**
     * @test
     */
    public function followUser_ログインしていない場合にユーザーをフォローできないこと()
    {
        $following_user = User::first();
        $followed_user  = User::find(2);

        $this->get(route('user.show', ['id' => $followed_user->id], false));

        $response = $this->post(route('user.follow', ['id' => $followed_user->id], false));
        $response->assertStatus(302);
        $response->assertLocation(route('login'));

        $this->assertFalse($following_user->isFollowing($followed_user));
    }

    /**
     * @test
     */
    public function unfollowUser_ログインしている場合にユーザーをアンフォローできること()
    {
        $following_user = User::first();
        $followed_user  = User::find(2);
        $following_user->follow($followed_user);

        $this->actingAs($following_user);

        $this->get(route('user.show', ['id' => $followed_user->id], false));

        $response = $this->post(route('user.unfollow', ['id' => $followed_user->id], false));
        $response->assertStatus(302);
        $response->assertLocation(route('user.show', ['id' => $followed_user->id], false));

        $this->assertfalse($following_user->isFollowing($followed_user));
    }

    /**
     * @test
     */
    public function unfollowUser_ログインしていない場合にユーザーをアンフォローできないこと()
    {
        $following_user = User::first();
        $followed_user  = User::find(2);
        $following_user->follow($followed_user);

        $this->get(route('user.show', ['id' => $followed_user->id], false));

        $response = $this->post(route('user.unfollow', ['id' => $followed_user->id], false));
        $response->assertStatus(302);
        $response->assertLocation(route('login'));

        $this->assertTrue($following_user->isFollowing($followed_user));
    }
}


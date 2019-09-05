<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Board;
use App\Models\Comment;
use App\Models\Tag;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->create([
            'name' => 'foo', 'email' => 'foo@gmail.com'
        ]);

        factory(User::class, 10)->create();

        // tag
        $tags = factory(Tag::class, 5)->create();

        $max_id = User::max('id');
        User::all()->each(function(User $user) use ($max_id, $tags) {

            # １〜５人をランダムでフォローする
            for($i = 0; $i < rand(1, 5); $i++) {
                while(true) {
                    $following_id = rand(1, $max_id);
                    # 自分自身ではない && 既にフォローしていなければOK
                    if ($following_id !== $user->id && !$user->following()->find($following_id)) {
                        break;
                    }
                }

                $user->following()->attach($following_id);
            }

            # board,comments作成
            factory(Board::class, 3)->create(['user_id' => $user->id])->each(function(Board $board) use ($tags) {
                // コメント
                factory(Comment::class, rand(1, 3))->create([
                    'user_id' => $board->user_id, 'board_id' => $board->id
                ]);

                // タグ付け
                $board->tags()->attach($tags->random());
            });
        });
    }
}

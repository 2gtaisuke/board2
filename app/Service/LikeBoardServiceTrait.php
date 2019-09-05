<?php

namespace App\Service;

use App\Exceptions\LikeUserException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

trait LikeBoardServiceTrait
{
    /**
     * ユーザーと掲示版の存在を確認し、インスタンスを配列で返す
     *
     * @param int $user_id
     * @param int $board_id
     * @return array
     * @throws LikeUserException
     */
    protected function checkExistence(int $user_id, int $board_id): array
    {
        if (!($user = $this->user->find($user_id))) {
            throw new LikeUserException('ユーザーが存在しません');
        }

        if (!($board = $this->board->find($board_id))) {
            throw new LikeUserException('掲示版が存在しません');
        }

        return [$user, $board];
    }

    /**
     * 掲示版に『いいね』する
     *
     * @param int $user_id
     * @param int $board_id
     * @throws LikeUserException
     */
    public function likeBoard(int $user_id, int $board_id)
    {
        list($user, $board) = $this->checkExistence($user_id, $board_id);

        try {
            $user->like($board);
        } catch (\Exception $e) {
            throw new LikeUserException('ユーザーは既に掲示版を『いいね』しています');
        }
    }

    /**
     * 掲示版から『いいね』を外す
     *
     * @param int $user_id
     * @param int $board_id
     * @throws LikeUserException
     */
    public function offLikeBoard(int $user_id, int $board_id)
    {
        list($user, $board) = $this->checkExistence($user_id, $board_id);

        try {
            $user->offLike($board);
        } catch (\Exception $e) {
            throw new LikeUserException('ユーザーはまだ掲示版を『いいね』していません');
        }
    }

    /**
     * ユーザーが掲示版を『いいね』しているかどうかを返す
     *
     * @param int $user_id
     * @param int $board_id
     * @return mixed
     * @throws LikeUserException
     */
    public function isLikeBoard(int $user_id, int $board_id)
    {
        list($user, $board) = $this->checkExistence($user_id, $board_id);

        return $user->isLike($board);
    }

    /**
     * ユーザーが掲示版を『いいね』していない場合はする。逆の場合は外す
     *
     * @param int $user_id
     * @param int $board_id
     * @return bool
     * @throws LikeUserException
     */
    public function toggleLikeBoard(int $user_id, int $board_id): bool
    {
        list($user, $board) = $this->checkExistence($user_id, $board_id);

        // TODO: 外すとfalseが返る、という仕様はよくない？
        if ($user->isLike($board)) {
            $user->offLike($board);
            return false;
        } else {
            $user->like($board);
            return true;
        }
    }

    /**
     * ユーザーが『いいね』している掲示版を返す
     *
     * @param int $user_id
     * @return Collection
     * @throws LikeUserException
     */
    public function retrieveLikes(int $user_id): Collection
    {
        if (!($user = $this->user->find($user_id))) {
            throw new LikeUserException('ユーザーが存在しません');
        }

        return $user->likes;
    }
}
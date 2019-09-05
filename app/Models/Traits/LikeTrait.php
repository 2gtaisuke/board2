<?php


namespace App\Models\Traits;

use App\Models\Board;

trait LikeTrait
{
    /**
     * $boardを『いいね』する
     *
     * @param Board $board
     * @throws \Exception
     */
    public function like(Board $board): void
    {
        if ($this->isLike($board)) {
            throw new \Exception('既に『いいね』しています');
        }

        $this->likes()->attach($board->id);
    }

    /**
     * $boardから『いいね』を外す
     *
     * @param Board $board
     * @throws \Exception
     */
    public function offLike(Board $board)
    {
        if (!$this->isLike($board)) {
            throw new \Exception('まだ『いいね』していません');
        }

        $this->likes()->detach($board->id);
    }

    /**
     * $boardを『いいね』しているかどうか返す
     *
     * @param Board $board
     * @return bool
     */
    public function isLike(Board $board): bool
    {
        return !is_null($this->load('likes')->likes->find($board->id));
    }
}
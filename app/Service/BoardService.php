<?php

namespace App\Service;


use App\Models\Board;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

class BoardService
{
    /** @var Board */
    private $board;

    /** @var User */
    private $user;

    /** @var DatabaseManager */
    private $db_manager;

    /** @var TagService */
    private $tag_service;

    public function __construct(Board $board, User $user, DatabaseManager $db_manager, TagService $tag_service)
    {
        $this->board = $board;
        $this->user  = $user;
        $this->db_manager = $db_manager;
        $this->tag_service = $tag_service;
    }

    /**
     * コメント付き最新順でシンプルページネーションを取得する
     *
     * @param $login_user_id
     * @param int $per_page
     * @return Paginator
     */
    public function getPaginationOfLatest($login_user_id, $per_page = 5): Paginator
    {
//        TODO: scopeが聞いていない
        return $this->board->scopeLatestWithRelation($login_user_id)->simplePaginate($per_page);
    }


    /**
     * $idのBoardインスタンスを返す
     *
     * @param int $id
     * @return Board|null
     */
    public function find(int $id)
    {
        return $this->board->with('comments')->find($id);
    }

    /**
     * CommentクラスのPagination付きのBoardを返す
     *
     * @param int $id
     * @param int $per_page
     * @return Board
     */
    public function getWithPaginationOfComments($id = null, int $per_page = 100): Board
    {
        // TODO: Boardインスタンスの中にCommentのpaginationが欲しい。他の実装はないか
        $board = $this->board->with('tags')->find($id);
        $comments = $board->comments()->simplePaginate($per_page);
        $board->comments = $comments;

        return $board;
    }


    /**
     * 掲示版を作成する
     *
     * @param int $user_id
     * @param string $title
     * @param string $content
     * @param string $tags
     * @throws \Throwable
     */
    public function store(int $user_id, string $title, string $content, string $tags): void
    {
        if(!($user = $this->user->find($user_id))) {
            throw new \Exception('ユーザーが存在しません');
        }

        $this->db_manager->transaction(function() use ($user, $title, $content, $tags) {
            $created_board = $user->boards()->create([
                'title' => $title
            ]);
            $created_board->comments()->create([
                'content' => $content,
                'user_id' => $user->id
            ]);

            $tags = explode(' ', $tags);
            $created_tags = $this->tag_service->createIfNotExists($tags);
            $created_board->tags()->attach($created_tags);
        });
    }

    /**
     * コメントを作成する
     *
     * @param int $user_id
     * @param int $board_id
     * @param string $content
     * @return Comment
     * @throws \Exception
     */
    public function storeComment(int $user_id, int $board_id, string $content): Comment
    {
        if(!($user = $this->user->find($user_id))) {
            throw new \Exception('ユーザーが存在しません');
        }

        if(!($board = $this->board->find($board_id))) {
            throw new \Exception('掲示版が存在しません');
        }

        try {
            $comment = $user->comments()->create([
                'board_id' => $board->id,
                'content' => $content
            ]);
        } catch (QueryException $e) {
            throw new \Exception($e->getMessage());
        }

        return $comment;
    }

    /**
     * $board_idのタグCollectionを返す
     *
     * @param int $board_id
     * @return Collection
     * @throws \Exception
     */
    public function retrieveTags(int $board_id): Collection
    {
        if(!($board = $this->board->find($board_id))) {
            throw new \Exception('掲示版が存在しません');
        }

        return $this->board->find($board_id)->tags;
    }
}
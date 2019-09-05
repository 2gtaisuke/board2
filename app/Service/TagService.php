<?php
namespace App\Service;

use App\Models\Board;
use App\Models\Tag;
use Illuminate\Database\DatabaseManager;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

class TagService
{
    /** @var Tag */
    private $tag;

    /** @var Board */
    private $board;

    /** @var DatabaseManager */
    private $database_manager;

    public function __construct(Tag $tag, Board $board, DatabaseManager $database_manager)
    {
        $this->tag = $tag;
        $this->board = $board;
        $this->database_manager = $database_manager;
    }

    /**
     * タグごとのボード数を取得する
     *
     * @return Collection
     */
    public function getBoardCountPerTag(): Collection
    {
        // select t.id, t.name, count('x') from tags as t inner join board_tag as bt on t.id = bt.tag_id inner join boards as b on b.id = bt.board_id group by t.id;
        return $this->database_manager
            ->table('tags')
            ->join('board_tag', 'tags.id', '=', 'board_tag.tag_id')
            ->join('boards', 'boards.id', '=', 'board_tag.board_id')
            ->groupBy('tags.name')
            ->select('tags.name', $this->database_manager->raw("count('x') as board_count"))
            ->get();
    }

    /**
     * $tag_nameのついた掲示版コレクションを返す
     *
     * @param string $tag_name
     * @param string|null $user_id
     * @param int $per_page
     * @return Paginator
     * @throws \Exception
     */
    public function retrieveBoardPaginationByTagName(string $tag_name, string $user_id = null, int $per_page = 5): Paginator
    {
        if(!($tag = $this->tag->where('name', '=', $tag_name))) {
            throw new \Exception('タグが存在しません');
        }

        return $this->board
            ->scopeLatestWithRelation($user_id)
            ->whereHas('tags', function($query) use ($tag_name) {
                return $query->where('name', '=', $tag_name);
            })
            ->simplePaginate($per_page);
    }

    /**
     * $tag_namesのタグが存在しなければ作成する。
     *
     * @param array $tag_names
     * @return Collection
     * @throws \Exception
     */
    public function createIfNotExists(array $tag_names): Collection
    {
        // TODO: modelとcollectionのapiを理解してないからこういうコードを書く羽目になる
        // TODO: 書き直すこと

        // 重複削除
        $tag_names = array_unique($tag_names);

        // 既存の抽出
        $existed_tags = $this->tag->whereIn('name', $tag_names)->get();

        // 存在しないタグを抽出
        $not_exists_tags = array_filter($tag_names, function($tag_name) use ($existed_tags) {
            return !$existed_tags->contains('name', $tag_name);
        });
        $not_exists_tags = array_map(function($tag_name) {
            return ['name' => $tag_name];
        }, $not_exists_tags);

        $this->database_manager->table('tags')->insert($not_exists_tags);

        $tag_collection = $this->tag->whereIn('name', $tag_names)->get();

        return $tag_collection;
    }
}
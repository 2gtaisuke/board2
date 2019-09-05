<?php

namespace App\Http\Controllers;

use App\Service\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /** @var TagService */
    private $tag_service;

    public function __construct(TagService $tag_service)
    {
        $this->tag_service = $tag_service;
    }

    /**
     * タグ一覧を表示する
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $tags = $this->tag_service->getBoardCountPerTag();
        return view('tag.index', compact('tags'));
    }

    /**
     * $tag_nameの掲示版一覧を表示する
     *
     * @param Request $request
     * @param string $tag_name
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function showBoard(Request $request, string $tag_name)
    {
//        TODO: 画面テストを書く
        $login_user_id = $request->user() ? $request->user()->id : null;
        $boards = $this->tag_service->retrieveBoardPaginationByTagName(
            $tag_name,
            $login_user_id,
            config('board.index.comments_per_page')
        );

        return view('board.index', compact('boards'));
    }
}
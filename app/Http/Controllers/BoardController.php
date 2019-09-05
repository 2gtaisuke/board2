<?php

namespace App\Http\Controllers;

use App\Exceptions\LikeUserException;
use App\Http\Requests\StoreBoardRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Board;
use App\Service\BoardService;
use App\Service\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psr\Log\LoggerInterface;

class BoardController extends Controller
{
    /** @var BoardService */
    private $board_service;

    /** @var UserService */
    private $user_service;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(BoardService $board_service, UserService $user_service, LoggerInterface $logger)
    {
        $this->board_service = $board_service;
        $this->user_service = $user_service;
        $this->logger = $logger;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $login_user_id = $request->user() ? $request->user()->id : null;

        $boards = $this->board_service->getPaginationOfLatest($login_user_id, config('board.index.comments_per_page'));
        return view('board.index', compact('boards'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('board.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBoardRequest $request)
    {
        $login_user = $request->user();
        $attribute = $request->all();

        try {
            $this->board_service->store(
                $login_user->id,
                $attribute['board']['title'],
                $attribute['comment']['content'],
                $attribute['tags']
            );
        } catch (\Throwable $e) {
            // TODO: アラート
            $this->logger->error($e);
            return back();
        }

        return redirect()->route('board.index');
    }

    public function storeComment(int $board_id, StoreCommentRequest $request)
    {
        $login_user = $request->user();
        $attribute = $request->all();

        try {
            $this->board_service->storeComment($login_user->id, $board_id, $attribute['comment']['content']);
        } catch (\Exception $e) {
            // TODO: アラート
            $this->logger->error($e);
            return back();
        }

        return redirect()->route('board.show', ['id' => $board_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id, Request $request)
    {
        $login_user_id = $request->user() ? $request->user()->id : null;

        /** @var Board $board */
        $board = $this->board_service->getWithPaginationOfComments($id, config('board.show.comments_per_page'));

        return view('board.show', compact('board'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        TODO: 実装
    }

    /**
     * 『いいね』をトグルさせる
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function toggleLike(int $id, Request $request)
    {
        // TODO: 書き直す。
        $login_user = $request->user();
        $board = $this->board_service->find($id);

        try {
            $is_liked = $this->user_service->toggleLikeBoard($login_user->id, $board->id);
        } catch (LikeUserException $e) {
            return response()
                ->json([
                    'status' => 'failed'
                ]);
        }

        return response()
            ->json([
                'status' => 'success',
                'liked'  => $is_liked
            ]);
    }
}

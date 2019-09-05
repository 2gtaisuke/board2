<?php

namespace App\Service;

use App\Models\Board;
use App\Models\User;
use GuzzleHttp\ClientInterface as Guzzle;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Storage;

class UserService
{
    use FollowUserServiceTrait, LikeBoardServiceTrait;

    /** @var User */
    private $user;

    /** @var Board */
    private $board;

    /** @var Guzzle */
    private $guzzle;

    /** @var DatabaseManager */
    private $database_manager;

    public function __construct(User $user, Board $board, Guzzle $guzzle, DatabaseManager $database_manager)
    {
        $this->user = $user;
        $this->board = $board;
        $this->guzzle = $guzzle;
        $this->database_manager = $database_manager;
    }

    /**
     * Userモデルを保存し、作成したモデルを返す
     *
     * @param array $attributes
     * @return User
     */
    public function store($attributes): User
    {
        return $this->user->create($attributes);
    }

    /**
     * プロフィール画像を保存する
     * @param string $file_url
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function storeImage(string $file_url): string
    {
        // TODO: ファイルシステム触るときにもっと例外とか色々対処したほうが良い
        try {
            $response = $this->guzzle->request('GET', $file_url);

        } catch (ClientException | RequestException | BadResponseException $e) {
            throw new \Exception($e->getMessage());
        }

        $content_type = $response->getHeaderLine('content-type');
        $extenstion = substr($content_type, strrpos($content_type, '/') + 1);

        $dest_name = uniqid() . '.' . $extenstion;

        Storage::put($dest_name, $response->getBody(), 'public');

        return $dest_name;
    }

    /**
     * ユーザーのプロフィール画像のパスを返す
     *
     * @param User $user
     * @return mixed
     */
    public function getUserProfileImage(User $user)
    {
        return Storage::url($user->image_path ?? 'unknown_user.jpeg');
    }

    /**
     * プロフィール画像を削除する
     *
     * @param string $file_path
     * @return bool
     */
    public function deleteImage(string $file_path): bool
    {
        return Storage::delete($file_path);
    }
}
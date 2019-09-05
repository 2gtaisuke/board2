<?php


namespace App\Repositories;


use App\Models\SocialAccount;

class SocialAccountRepository implements SocialAccountRepositoryInterface
{
    /** @var SocialAccount */
    private $social_account;

    public function __construct(SocialAccount $social_account)
    {
        $this->social_account = $social_account;
    }

    /**
     * 最初に見つかったSocialAccountモデルを返す。そうでなければインスタンスを生成し返す
     *
     * @param array $attributes
     * @return SocialAccount
     */
    public function firstOrNew(array $attributes): SocialAccount
    {
        return $this->social_account->firstOrNew($attributes);
    }
}
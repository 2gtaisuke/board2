<?php
namespace App\Http\View\Composers;

use App\Service\UserService;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginUserComposer
{

    /** @var Request */
    private $request;

    /** @var Filesystem */
    private $filesystem;

    /** @var UserService */
    private $user_service;

    public function __construct(Request $request, Filesystem $filesystem, UserService $user_service)
    {
        $this->request = $request;
        $this->filesystem = $filesystem;
        $this->user_service = $user_service;
    }

    public function compose(View $view)
    {
        $user = $this->request->user();
        if($user) {
            $user_profile_image = $this->user_service->getUserProfileImage($user);
            $view->with('login_user_profile_image', $user_profile_image);

        }
        $view->with('login_user', $this->request->user());
    }
}
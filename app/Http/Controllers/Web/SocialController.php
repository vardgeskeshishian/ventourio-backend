<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\SocialAuth\SocialAccountConnectRequest;
use App\Models\User;
use App\Models\UserSocial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use function redirect;

class SocialController extends Controller
{
    /**
     * @var string
     */
    private string $redirectUrl;

    public function __construct()
    {
        $this->redirectUrl = config('front.web_url') . '/callback';
    }


    /**
     * Handle Social login request
     *
     * @param $provider
     * @return RedirectResponse
     */
    public function socialLogin($provider): RedirectResponse
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * Obtain the user information from Social Logged in.
     * @param $provider
     * @return RedirectResponse
     */
    public function handleProviderCallback($provider, Request $request):RedirectResponse
    {

        DB::beginTransaction();
        try{

            $userSocial = Socialite::driver($provider)->stateless()->user();

            $email = $userSocial->getEmail();

            $providerId = $userSocial->getId();

            $hasMetaData = $request->input('state');

            $user = null;

            if($hasMetaData)
            {
                parse_str($request->input('state'), $metaData);

                $this->redirectUrl = $metaData['redirect_url'];

                $user = User::query()->findOrFail($metaData['user_id']);

            }

            if(!$user){

                $user = User::query()->firstOrCreate(
                    ['email' =>  $email],
                    [
                        'first_name' => $userSocial->getName(),
                        'password' => Hash::make(Str::random(10)),
                    ]
                );
            }

            if( ! $user->hasSocialAccount($provider))
            {

                if($this->suchSocialAccountExists($providerId))
                {
                    throw new \Exception(__('errors.app.user.provider_user_already_exists', ['provider' => $provider]));
                }

                $this->createSocialAccount($user, $provider, $providerId);
            }

            $token = !$hasMetaData ? $user->createToken("API TOKEN")->plainTextToken : null;

            DB::commit();

            return redirect( $this->redirectUrl . "?status=success" . ($token ? "&token={$token}" : ''));

        }catch(\Exception $e){

            Log::error($e->getMessage());
            DB::rollBack();
            return redirect($this->redirectUrl . "?status=error&message={$e->getMessage()}" );

        }
    }

    /**
     * Handle Social connect request
     *
     * @param $provider
     */
    public function connectSocialLogin(SocialAccountConnectRequest $request, $provider)
    {
        if(auth()->user()->hasSocialAccount($provider))
        {
            return response()->json([
                'success' => false,
                'message' => __('errors.app.user.have_already_social_account', ['provider' => $provider]),
            ]);
        }

        $redirectUrl = request()->query('redirect_url');

        $generateUrl = Socialite::driver($provider)
            ->with(['state' => 'user_id=' . auth()->id() . '&redirect_url='. $redirectUrl ])
            ->stateless()
            ->redirect()
            ->getTargetUrl();

        return response()->json([
            'success' => true,
            'data' => $generateUrl
        ]);
    }

    private function createSocialAccount($user, string $provider, string $providerId): void
    {
        $user->socialAccounts()->create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_id' => $providerId,
        ]);
    }

    private function suchSocialAccountExists($providerId):bool
    {
        return UserSocial::where('provider_id', $providerId)->exists();
    }

    public function delete($provider)
    {

        $socialAccount = auth()->user()
                               ->socialAccounts()
                               ->where('provider', $provider)
                               ->firstOrFail();

        $socialAccount->delete();

        return response()->json([
            'success' => true,
            'message' => __('success.common.success'),
        ]);

    }
}

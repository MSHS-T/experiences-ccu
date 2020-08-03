<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use JWTAuth;
use JWTAuthException;

class AuthController extends Controller
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwtauth', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $captcha_token = request('g-recaptcha-response');
        if (empty($captcha_token)) {
            return response()->json(['error' => 'Missing captcha content'], 400);
        }

        $client = new \GuzzleHttp\Client();

        $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params' => [
                'secret' => env('RECAPTCHA_SECRET'),
                'response' => $captcha_token,
                'remoteip' => $request->ip()
            ]
        ]);
        $responseBody = json_decode((string) $response->getBody());
        if (!$responseBody->success) {
            return response()->json(['error' => 'Captcha validation failed'], 400);
        }

        $credentials = request(['email', 'password']);
        $rememberMe = boolval(request('remember_me', 0));

        $auth = auth();
        if ($rememberMe) {
            // Session will expire in 30 days if remember me is checked (1 hour otherwise)
            $auth = $auth->setTTL(60 * 24 * 30);
        }

        if (!$token = $auth->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = \App\User::with('roles')->find(auth()->user()->id);
        return response()->json($user);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_at' => time() + (auth()->factory()->getTTL() * 60)
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use OpenVPN\Config;

class AuthController extends Controller
{

    /**
     * @OA\Post(
     * path="/register",
     * summary="Register",
     * description="Register by name, email, password",
     * operationId="authRegister",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"name", "email", "password"},
     *       @OA\Property(property="email", type="string", format="email", example="example@example.com"),
     *       @OA\Property(property="name", type="string", format="name", example="example"),
     *       @OA\Property(property="password", type="string", format="password", example="123456"),
     *    ),
     * ),
     * @OA\Response(
     *    response=404,
     *    description="Name Or Email has already been taken",
     *    @OA\JsonContent(
     *       @OA\Property(property="result", type="string", example="error"),
     *       @OA\Property(property="error", type="string", example="The name has already been taken.")
     *    )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="result", type="string", example="success"),
     *       @OA\Property(property="api_token", type="string", example="OzQ50ke3GElJMNvBZm8uksngp8dqNVYAHqr5CGHN9visYI0TYHg1fFdhsNf8BqTpwqDwXqcPhcxzN3Pj")
     *        )
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse {

        $input = $request->all();

        $rules = array(
            'email' => 'required|string|email|max:255|unique:users',
            'name' => 'required|string|unique:users',
            'password' => 'required|string'
        );

        $messages = array(
            'email.required|string|email|max:255|unique:users' => "",
            'name.required|string|unique:users' => "",
        );

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        $email = $input['email'];
        $name = $input['name'];
        $password = Hash::make($input['password']);
        $api_token = Str::random(80);

        User::forceCreate([
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'api_token' => $api_token,
        ]);

        return $this->sendSuccess("Registration success");
    }

    /**
     * @OA\Post(
     * path="/generate",
     * summary="Register",
     * description="Register by name, email, password",
     * operationId="generate",
     * tags={"Auth"},
     * @OA\Response(
     *    response=404,
     *    description="",
     *    @OA\JsonContent(
     *       @OA\Property(property="result", type="string", example="error"),
     *       @OA\Property(property="error", type="string", example="")
     *    )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="result", type="string", example="success"),
     *       @OA\Property(property="api_token", type="string", example="")
     *        )
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function generate(): JsonResponse {
        // Config object
        $config = new Config();

// Set client options
        $config->client();
        $config->dev             = 'tun';
        $config->proto           = 'tcp';
        $config->resolvRetry     = 'infinite';
        $config->redirectGateway = true;
        $config->keyDirection    = 1;
        $config->remoteCertTls   = 'server';
        $config->authUserPass    = true;
        $config->authNocache     = true;
        $config->nobind          = true;
        $config->persistKey      = true;
        $config->persistTun      = true;
        $config->compLzo         = false;
        $config->verb            = 3;
        $config->port = 13555;
        $config->ifconfigPoolPersist = "";

        $config->auth = "SHA256";
        $config->cipher = 'AES-256-CB';
        $config->keepalive = "10 120";
        $config->setRoute("192.168.150.0 255.255.255.0");
        $config->server = "10.0.0.0 255.255.255.0";
        $config->group = "nobody";

// Set additional certificates of client
        $config->setCerts([
            'ca'   => '/etc/openvpn/keys/ca.crt',
            'cert' => '/etc/openvpn/keys/issued/client1.crt',
            'key'  => '/etc/openvpn/keys/private/client1.key',
        ], true); // true - mean embed certificates into config, false by default

// Generate config by options
        Storage::disk('public')->put("vpn", $config->generate());

        return $this->sendResponse($config->generate(), "vpn");
    }
}

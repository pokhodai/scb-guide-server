<?php

namespace App\Http\Controllers\base;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\base\SwaggerAnnotations\MemorySwaggerAnnotation;

/**
 * @OA\Info(
 *    title="SCB GUIDE Api",
 *    version="1.0.2",
 * )
 * * @OA\Server(
 *     description="SCB_GUIDE",
 *     url="http://ovz6.j04713753.gmzem.vps.myjino.ru/public/api/"
 * )
 * @OA\SecurityScheme(
 *    securityScheme="Authorization",
 *    in="header",
 *    name="Authorization",
 *    type="http",
 *    scheme="bearer",
 *    bearerFormat="JWT",
 * ),
 */
class SwaggerController extends Controller {

}

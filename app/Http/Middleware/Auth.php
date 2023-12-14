<?php
/**
 * @author Dracowyn
 * @since 2023-12-14 17:09
 */

namespace App\Http\Middleware;

use App\Models\Business\Business as BusinessModel;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return JsonResponse
     */
    public function handle(Request $request,Closure $next): JsonResponse
    {
        $businessId = $request->input('busid',0);

        $business = BusinessModel::find($businessId);

        if (!$business) {
            return new JsonResponse([
                'msg' => '用户不存在',
                'data' => null,
                'code' => 0
            ]);
        }

        return $next($request);
    }
}

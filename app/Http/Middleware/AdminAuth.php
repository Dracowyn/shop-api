<?php
/**
 * 管理员身份验证中间件
 * @author Dracowyn
 * @since 2023-12-29 17:03
 */

namespace App\Http\Middleware;

use App\Models\Admin\Admin as AdminModel;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAuth
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $closure
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $closure)
    {
        $adminId = $request->input('adminid', '');

        $admin = AdminModel::find($adminId);

        if (!$admin) {
            return new JsonResponse([
                'code' => 401,
                'msg' => '请先登录',
                'data' => null,
            ]);
        }

        if ($admin->status !== 'normal') {
            return new JsonResponse([
                'code' => 403,
                'msg' => '账号已被禁用',
                'data' => null,
            ]);
        }

        $request->attributes->add(['admin' => $admin]);

        return $closure($request);
    }
}

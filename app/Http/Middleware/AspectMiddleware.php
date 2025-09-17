<?php

namespace App\Http\Middleware;

use App\Models\File;
use App\Models\LogRecord;
use App\Models\User_Group;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AspectMiddleware
{
    /**
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $this->beforeAspect($request);
        Log::info('AspectMiddleware is active', ['request_uri' => $request->getRequestUri()]);

        try {
            $response = $next($request);
            $this->afterAspect($request, $response);
            return $response;

        } catch (Exception $e) {
            $this->onExceptionAspect($request, $e);
            throw $e;
        }
    }

    protected function beforeAspect(Request $request)
    {
        $fileIds = is_array($request->file_id) ? $request->file_id : [$request->file_id];
        $groupId = $request->input('group_id');
        if (!$groupId || $fileIds) {

            $groupId = $this->getGroupIdFromFile($fileIds);
        }

        $methodName = $this->getActionName($request);
        if (Auth::check()) {
            foreach ($fileIds as $fileId) {
                LogRecord::query()->create([
                    'user_id' => Auth::id(),
                    'group_id' => $groupId,
                    'file_id' => $fileId,
                    'action' => 'Started  request : ' . $methodName,
                    'details' => json_encode([
                        'request' => $request->all(),
                    ]),
                    'action_time' => Carbon::now()
                ]);
            }

        }
        Log::info('Before Aspect Executed', ['user_id' => Auth::id(), 'group_id' => $groupId, 'action' => $methodName]);
    }

    protected function getGroupIdFromFile(array $fileIds): ?int
    {
        try {
            $file = File::query()->where('id', $fileIds[0])->firstOrFail();
            $userGroupId = $file->user_group_id;
            $userGroup = User_Group::query()->where('id', $userGroupId)->firstOrFail();
            return $userGroup->group_id;
        } catch (Exception $e) {
            Log::error('Failed to extract group_id from file_id', ['file_ids' => $fileIds, 'error' => $e->getMessage()]);
            return null;
        }
    }

    protected function getActionName(Request $request): string
    {
        $fullActionName = $request->route()->getActionName();
        $actionParts = explode('@', $fullActionName);
        return end($actionParts);
    }

    protected function afterAspect(Request $request, $response)
    {
        $fileIds = is_array($request->file_id) ? $request->file_id : [$request->file_id];
        $groupId = $request->input('group_id');
        if (!$groupId || $fileIds) {
            $groupId = $this->getGroupIdFromFile($fileIds);
        }
        $methodName = $this->getActionName($request);
        $responseContent = json_decode($response->getContent(), true); // Decode the entire content
        $message = isset($responseContent['message']) ? $responseContent['message'] : null; // Safely access the message
        $status = $response->getStatusCode();

        foreach ($fileIds as $fileId) {
            LogRecord::query()->create([
                'user_id' => Auth::id(),
                'file_id' => $fileId,
                'group_id' => $groupId,
                'action' => 'Completed  request : ' . $methodName,
                'details' => json_encode([
                    'status' => $status,
                    'response_message' => $message,
                ]),
                'action_time' => Carbon::now()
            ]);
        }
        Log::info('After Aspect Executed', ['user_id' => Auth::id(), 'group_id' => $groupId, 'action' => $methodName]);
    }

    protected function onExceptionAspect(Request $request, $e)
    {
        if ($e instanceof Exception) {
            $message = $e->getMessage();
        } else {
            $message = 'An unexpected error occurred';
        }

        $fileIds = $request->has('file_id')
            ? (is_array($request->file_id) ? $request->file_id : [$request->file_id])
            : [];

        $groupId = $request->input('group_id') ?? $this->getGroupIdFromFile($fileIds);

        $methodName = $this->getActionName($request);

        foreach ($fileIds as $fileId) {
            LogRecord::query()->create([
                'user_id' => Auth::id(),
                'file_id' => $fileId,
                'group_id' => $groupId,
                'action' => 'Failed Request : ' . $methodName,
                'details' => json_encode([
                    'error' => $message,
                    'request' => $request->all()
                ]),
                'action_time' => Carbon::now()
            ]);
        }

        Log::error('Exception Aspect Executed', [
            'user_id' => Auth::id(),
            'group_id' => $groupId,
            'action' => $methodName,
            'error' => is_string($e) ? $e : ($e instanceof Exception ? $e->getMessage() : 'Unknown error'),
            'trace' => $e instanceof Exception ? $e->getTraceAsString() : '',
            'request' => $request->all(),
        ]);
    }
}

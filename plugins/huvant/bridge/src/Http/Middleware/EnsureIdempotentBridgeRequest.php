<?php

namespace Huvant\Bridge\Http\Middleware;

use Closure;
use Huvant\Bridge\Models\IdempotencyKey;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotentBridgeRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = trim((string) $request->header('Idempotency-Key'));

        if ($key === '') {
            return $next($request);
        }

        if (mb_strlen($key) > 255) {
            return response()->json(['message' => 'The Idempotency-Key header may not exceed 255 characters.'], 422);
        }

        $userId = $request->user()?->getAuthIdentifier();
        if ($userId === null) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $requestHash = $this->requestHash($request);

        IdempotencyKey::query()->where('expires_at', '<', now())->delete();

        DB::beginTransaction();
        try {
            $record = IdempotencyKey::query()
                ->where('user_id', $userId)
                ->where('key', $key)
                ->lockForUpdate()
                ->first();

            if ($record?->expires_at?->isPast()) {
                $record->delete();
                $record = null;
            }

            if ($record) {
                $response = $this->replayOrReject($record, $requestHash);
                DB::commit();

                return $response;
            }

            $record = IdempotencyKey::query()->create([
                'user_id'      => $userId,
                'key'          => $key,
                'request_hash' => $requestHash,
                'state'        => 'processing',
                'expires_at'   => now()->addHours((int) config('huvant-bridge.idempotency.ttl_hours', 24)),
            ]);
            $response = $next($request);

            if ($response->getStatusCode() >= 500) {
                DB::rollBack();

                return $response;
            }

            $record->update([
                'state'           => 'completed',
                'response_status' => $response->getStatusCode(),
                'response_body'   => $response->getContent(),
            ]);
            DB::commit();

            return $response;
        } catch (QueryException $exception) {
            DB::rollBack();

            $record = IdempotencyKey::query()
                ->where('user_id', $userId)
                ->where('key', $key)
                ->first();

            if ($record) {
                return $this->replayOrReject($record, $requestHash);
            }

            throw $exception;
        } catch (\Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    private function replayOrReject(IdempotencyKey $record, string $requestHash): Response
    {
        if (! hash_equals($record->request_hash, $requestHash)) {
            return response()->json(['message' => 'This Idempotency-Key was already used with a different request.'], 409);
        }

        if ($record->state !== 'completed') {
            return response()->json(['message' => 'A request with this Idempotency-Key is already being processed.'], 409);
        }

        return response($record->response_body ?? '', $record->response_status ?? 200)
            ->header('Content-Type', 'application/json')
            ->header('Idempotency-Replayed', 'true');
    }

    private function requestHash(Request $request): string
    {
        $payload = [
            'method' => $request->method(),
            'path'   => $request->path(),
            'input'  => $this->canonicalize($request->input()),
            'files'  => $this->canonicalizeFiles($request->allFiles()),
        ];

        return hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
    }

    private function canonicalize(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (! array_is_list($value)) {
            ksort($value);
        }

        return array_map(fn (mixed $item): mixed => $this->canonicalize($item), $value);
    }

    private function canonicalizeFiles(array $files): array
    {
        $canonical = [];
        ksort($files);

        foreach ($files as $key => $file) {
            if (is_array($file)) {
                $canonical[$key] = $this->canonicalizeFiles($file);

                continue;
            }

            if ($file instanceof UploadedFile) {
                $canonical[$key] = [
                    'name'   => $file->getClientOriginalName(),
                    'size'   => $file->getSize(),
                    'sha256' => hash_file('sha256', $file->getRealPath()),
                ];
            }
        }

        return $canonical;
    }
}

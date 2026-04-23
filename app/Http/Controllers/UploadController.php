<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessVideoUpload;

use Illuminate\Support\Facades\Mail;
use App\Mail\UploadSuccessMail;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class UploadController extends Controller
{
    /**
     * Store a single video chunk
     */
    public function uploadChunk(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required',
                'chunkIndex' => 'required',
                'uploadId' => 'required'
            ]);

            $uploadId = $request->uploadId;

            $path = storage_path("app/chunks/{$uploadId}");
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $request->file('file')->move($path, "chunk_{$request->chunkIndex}");

            return response()->json(['status' => 'ok'], 200);

        } catch (\Exception $e) {
            Log::error("Chunk Upload Failed: " . $e->getMessage(), [
                'uploadId' => $request->uploadId ?? null,
                'chunkIndex' => $request->chunkIndex ?? null,
            ]);

            return response()->json(['error' => 'Chunk upload failed'], 500);
        }
    }


    /**
     * Final request when all chunks uploaded — start queue process.
     */
    public function completeUpload(Request $request)
    {
        try {
            $request->validate([
                'uploadId' => 'required',
                'fileName' => 'required'
            ]);

            ProcessVideoUpload::dispatch($request->uploadId, $request->fileName);

            Log::info("Upload Completed. Queued processing for file: {$request->fileName}");

            return response()->json(['status' => 'processing'], 200);

        } catch (\Exception $e) {

            Log::error("Failed to dispatch job: " . $e->getMessage());

            return response()->json(['error' => 'Failed to process upload'], 500);
        }
    }
}

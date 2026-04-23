<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\UploadSuccessMail;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProcessVideoUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 600; // 10 minutes
    protected $uploadId, $fileName;

    /**
     * Create a new job instance.
     */
    public function __construct($uploadId, $fileName)
    {
        $this->uploadId = $uploadId;
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            Log::info("Start processing: {$this->fileName}");

            $chunkPath = storage_path("app/chunks/{$this->uploadId}");
            $finalPath = storage_path("app/temp/{$this->fileName}");
            $finalPath = str_replace('\\', '/', $finalPath);

            if (!file_exists(dirname($finalPath))) {
                mkdir(dirname($finalPath), 0777, true);
            }

            $output = fopen($finalPath, 'wb');

            $chunks = glob("{$chunkPath}/chunk_*");
            sort($chunks);

            foreach ($chunks as $chunk) {
                fwrite($output, file_get_contents($chunk));
            }

            fclose($output);

            Log::info("Chunks merged successfully.");

            // Upload to Cloudinary
            $uploadResult = Cloudinary::uploadApi()->upload(
                $finalPath,
                [
                    'upload_preset' => config('filesystems.disks.cloudinary.upload_preset'),
                    'resource_type' => 'auto',
                    'folder' => 'uploads',
                ]
            );

            if (!$uploadResult || !isset($uploadResult['secure_url'])) {
                throw new \Exception("Upload failed - invalid response");
            }

            $uploadedUrl = $uploadResult['secure_url'];

            Log::info("Video uploaded successfully to Cloudinary.", [
                'url' => $uploadedUrl
            ]);

            // Cleanup temporary files
            foreach ($chunks as $chunk) {
                unlink($chunk);
            }

            rmdir($chunkPath);
            unlink($finalPath);

            Log::info("Temporary files deleted.");

            // Send Email
            $adminEmail = config('mail.admin_email');

            if (!$adminEmail) {
                Log::warning("Admin email not set in environment. Skipping email notification.");
                return;
            }

            Mail::to($adminEmail)
                ->send(new UploadSuccessMail($uploadedUrl, $this->fileName));

            Log::info("Email sent successfully.");

        } catch (\Exception $e) {

            Log::error("Processing Failed: " . $e->getMessage(), [
                'file' => $this->fileName,
                'uploadId' => $this->uploadId
            ]);
        }
    }
}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SimuPhish Uploader</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .upload-card {
            max-width: 450px;
            width: 100%;
            border-radius: 15px;
        }

        .progress {
            height: 18px;
        }

        .percentage-text {
            font-weight: 600;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="card shadow-lg p-4 upload-card">
    <h4 class="text-center mb-4">SimuPhish Chunked Uploader</h4>

    <input type="file" id="fileInput" class="form-control mb-3" accept="video/*" required>

    <button id="uploadBtn" class="btn btn-primary w-100">
        Upload File
    </button>

    <!-- Progress -->
    <div class="progress mt-4 d-none">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
    </div>

    <div class="percentage-text mt-2 d-none">0%</div>
</div>

<script>
function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

document.getElementById('uploadBtn').addEventListener('click', async function () {

    const file = document.getElementById('fileInput').files[0];

    if (!file) {
        document.getElementById('fileInput').classList.add('is-invalid');
        alert('Please select a file to upload.');
        return;
    }

    const progressBar = document.querySelector('.progress-bar');
    const progressContainer = document.querySelector('.progress');
    const percentText = document.querySelector('.percentage-text');
    const btn = document.getElementById('uploadBtn');

    progressContainer.classList.remove('d-none');
    percentText.classList.remove('d-none');
    btn.disabled = true;

    const chunkSize = 2 * 1024 * 1024; // 2MB chunks
    const uploadId = Date.now();
    const totalChunks = Math.ceil(file.size / chunkSize);

    for (let i = 0; i < totalChunks; i++) {

        const chunk = file.slice(i * chunkSize, (i + 1) * chunkSize);

        let formData = new FormData();
        formData.append("file", chunk);
        formData.append("chunkIndex", i);
        formData.append("uploadId", uploadId);

        await fetch('/upload-chunk', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken() },
            body: formData
        });

        let percent = Math.round(((i + 1) / totalChunks) * 100);

        progressBar.style.width = percent + "%";
        progressBar.innerText = percent + "%";
        percentText.innerText = percent + "%";
    }

    await fetch('/upload-complete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken()
        },
        body: JSON.stringify({
            uploadId: uploadId,
            fileName: file.name
        })
    });

    // Redirect after success
    window.location.href = "/thank-you";
});
</script>

</body>
</html>

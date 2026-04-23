<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Upload Completed - Admin Notification</title>

<style>
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: #eef1f7;
        padding: 25px;
        margin: 0;
    }

    .container {
        max-width: 620px;
        margin: auto;
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .header {
        background: linear-gradient(135deg, #4f46e5, #4338ca);
        color: white;
        padding: 25px;
        text-align: center;
    }

    .header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
    }

    .content {
        padding: 30px 25px;
        line-height: 1.6;
        font-size: 15px;
        color: #333;
    }

    .content h3 {
        color: #111827;
        margin-bottom: 12px;
    }

    .info-box {
        background: #f9fafb;
        border-left: 4px solid #4f46e5;
        padding: 12px 15px;
        margin: 18px 0;
        border-radius: 6px;
        font-size: 14px;
    }

    .btn {
        display: inline-block;
        padding: 12px 20px;
        background: #4f46e5;
        color: #fff !important;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 500;
        margin-top: 15px;
    }

    .btn:hover {
        background: #4338ca;
    }

    .footer {
        background: #f9fafb;
        padding: 18px;
        font-size: 12px;
        text-align: center;
        color: #666;
        border-top: 1px solid #e5e7eb;
    }
</style>

</head>

<body>

<div class="container">

    <div class="header">
        <h2>SimuPhish Upload System</h2>
    </div>

    <div class="content">
        <h3>Hello Admin,</h3>

        <p>
            This is to notify you that a file has been
            <strong>successfully uploaded and fully processed</strong> by the system.
        </p>

        <div class="info-box">
            <strong>Uploaded File:</strong> {{ $fileName ?? 'N/A' }} <br>
            <strong>Status:</strong> Successfully stored in Cloudinary <br>
            <strong>Timestamp:</strong> {{ now()->format('d M Y, h:i A') }}
        </div>

        <p>You may review the uploaded file using the link below:</p>

        <a href="{{ $uploadUrl ?? '#' }}" class="btn">View Uploaded File</a>

        <p style="margin-top: 25px;">
            If you notice any unusual activity or face issues accessing the file, please contact the technical team immediately.
        </p>

        <p>Regards,<br>
        <strong>SimuPhish System Notification Service</strong></p>
    </div>

    <div class="footer">
        © {{ date('Y') }} SimuPhish. Automated System Notification. All rights reserved.
    </div>

</div>

</body>
</html>

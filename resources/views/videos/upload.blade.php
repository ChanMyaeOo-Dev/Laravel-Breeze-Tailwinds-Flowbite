<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Video Upload to Supabase</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
        }

        .upload-area {
            border: 3px dashed #ddd;
            border-radius: 10px;
            padding: 50px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fafafa;
            margin-bottom: 20px;
        }

        .upload-area:hover,
        .upload-area.dragover {
            border-color: #667eea;
            background: #f0f0ff;
        }

        .upload-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .file-input {
            display: none;
        }

        .btn-select {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 15px;
            transition: background 0.3s;
        }

        .btn-select:hover {
            background: #5a67d8;
        }

        .file-info {
            display: none;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .file-info.show {
            display: block;
        }

        .file-details {
            margin-bottom: 15px;
        }

        .file-details p {
            margin: 5px 0;
            color: #555;
        }

        .file-name {
            font-weight: bold;
            color: #333;
            word-break: break-all;
        }

        .btn-upload {
            width: 100%;
            background: #48bb78;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-upload:hover:not(:disabled) {
            background: #38a169;
        }

        .btn-upload:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-cancel {
            width: 100%;
            background: #fc8181;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.3s;
        }

        .btn-cancel:hover {
            background: #f56565;
        }

        .progress-section {
            display: none;
            margin-bottom: 20px;
        }

        .progress-section.show {
            display: block;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background: #edf2f7;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #48bb78, #38a169);
            width: 0%;
            transition: width 0.3s;
            border-radius: 10px;
        }

        .progress-text {
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
        }

        .alert.show {
            display: block;
        }

        .alert-success {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .alert-error {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #feb2b2;
        }

        .video-url {
            word-break: break-all;
            margin-top: 10px;
            font-size: 12px;
            background: #edf2f7;
            padding: 10px;
            border-radius: 5px;
        }

        .limits {
            color: #a0aec0;
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>📹 Video Upload</h1>
            <p>Upload large videos directly to Supabase Storage</p>
        </div>

        <!-- Upload Area -->
        <div class="upload-area" id="uploadArea">
            <div class="upload-icon">☁️</div>
            <h3>Drag & Drop your video here</h3>
            <p>or</p>
            <button class="btn-select" onclick="document.getElementById('fileInput').click()">
                Select Video
            </button>
            <p class="limits">Maximum file size: {{ $maxUploadMb }}MB | Supported formats: MP4, MOV, WebM, AVI</p>
            <input type="file" id="fileInput" class="file-input" accept="video/*">
        </div>

        <!-- File Info -->
        <div class="file-info" id="fileInfo">
            <div class="file-details">
                <p><strong>File Name:</strong> <span class="file-name" id="fileName"></span></p>
                <p><strong>File Size:</strong> <span id="fileSize"></span></p>
                <p><strong>Type:</strong> <span id="fileType"></span></p>
            </div>
            <button class="btn-upload" id="uploadBtn" onclick="startUpload()">
                Upload to Supabase
            </button>
            <button class="btn-cancel" onclick="resetForm()">
                Cancel
            </button>
        </div>

        <!-- Progress Section -->
        <div class="progress-section" id="progressSection">
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <p class="progress-text" id="progressText">Preparing upload...</p>
        </div>

        <!-- Alerts -->
        <div class="alert alert-error" id="errorAlert"></div>
        <div class="alert alert-success" id="successAlert">
            <p><strong>✅ Upload Complete!</strong></p>
            <p>Your video has been uploaded successfully.</p>
            <div class="video-url" id="videoUrl"></div>
        </div>
    </div>

    <script>
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // DOM Elements
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const fileType = document.getElementById('fileType');
        const uploadBtn = document.getElementById('uploadBtn');
        const progressSection = document.getElementById('progressSection');
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');
        const errorAlert = document.getElementById('errorAlert');
        const successAlert = document.getElementById('successAlert');
        const videoUrl = document.getElementById('videoUrl');

        let selectedFile = null;
        let uploadStartTime = null;
        const maxUploadBytes = {{ $maxUploadBytes }};
        const maxUploadMb = {{ $maxUploadMb }};

        // Click on upload area to trigger file input
        uploadArea.addEventListener('click', function(e) {
            // Don't trigger if clicking the select button
            if (e.target.tagName !== 'BUTTON') {
                fileInput.click();
            }
        });

        // File selected via input
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });

        // Drag and drop handlers
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('dragover');

            if (e.dataTransfer.files.length > 0) {
                handleFileSelect(e.dataTransfer.files[0]);
            }
        });

        // Handle file selection
        function handleFileSelect(file) {
            if (!file) return;

            // Validate file type
            const allowedTypes = ['video/mp4', 'video/quicktime', 'video/webm', 'video/x-msvideo', 'video/mov'];
            if (!allowedTypes.includes(file.type)) {
                showError('Invalid file type. Please select MP4, MOV, WebM, or AVI files.');
                return;
            }

            // Validate file size
            if (file.size > maxUploadBytes) {
                showError('File is too large. Maximum size is ' + maxUploadMb + 'MB.');
                return;
            }

            // Store file and show info
            selectedFile = file;
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileType.textContent = file.type;

            fileInfo.classList.add('show');
            errorAlert.classList.remove('show');
            successAlert.classList.remove('show');
            progressSection.classList.remove('show');
        }

        // Start upload process
        async function startUpload() {
            if (!selectedFile) return;

            // Disable button and show progress
            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Uploading...';
            progressSection.classList.add('show');
            progressFill.style.width = '0%';
            progressText.textContent = 'Getting upload URL...';
            errorAlert.classList.remove('show');
            successAlert.classList.remove('show');
            uploadStartTime = Date.now();

            try {
                // Step 1: Get presigned URL from server
                const response = await fetch('/videos/presigned-url', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        filename: selectedFile.name,
                        content_type: selectedFile.type,
                        file_size: selectedFile.size,
                    }),
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Failed to get upload URL');
                }

                console.log('Presigned URL obtained:', data.presigned_url);

                // Step 2: Upload directly to Supabase Storage
                progressText.textContent = '0% - Uploading...';
                await uploadToStorage(data.presigned_url, selectedFile);

                // Step 3: Confirm upload with server
                const confirmResponse = await fetch('/videos/confirm', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        file_path: data.file_path,
                    }),
                });

                const confirmData = await confirmResponse.json();

                if (!confirmData.success) {
                    throw new Error(confirmData.message || 'Failed to confirm upload');
                }

                // Show success
                console.log(data)
                successAlert.classList.add('show');
                videoUrl.textContent = data.public_url;

            } catch (error) {
                console.error('Upload failed:', error);
                showError(error.message || 'Upload failed. Please try again.');
            } finally {
                // Reset UI
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload to Supabase';
                if (!successAlert.classList.contains('show')) {
                    progressSection.classList.remove('show');
                }
            }
        }

        // Upload file directly to Supabase Storage
        function uploadToStorage(presignedUpload, file) {
            return new Promise(function(resolve, reject) {
                const xhr = new XMLHttpRequest();
                const uploadUrl = typeof presignedUpload === 'string' ? presignedUpload : presignedUpload.url;
                const uploadHeaders = typeof presignedUpload === 'string' ? {} : (presignedUpload.headers || {});

                if (!uploadUrl) {
                    reject(new Error('Upload URL was not returned by the server.'));
                    return;
                }

                // Track upload progress
                xhr.upload.addEventListener('progress', function(event) {
                    if (event.lengthComputable) {
                        const percentComplete = Math.round((event.loaded / event.total) * 100);
                        const elapsed = (Date.now() - uploadStartTime) / 1000;
                        const speed = event.loaded / elapsed;

                        progressFill.style.width = percentComplete + '%';
                        progressText.textContent =
                            percentComplete + '% - ' +
                            formatFileSize(event.loaded) + ' / ' +
                            formatFileSize(event.total) +
                            ' (' + formatFileSize(speed) + '/s)';
                    }
                });

                // Handle completion
                xhr.addEventListener('load', function() {
                    if (xhr.status === 200 || xhr.status === 204) {
                        resolve();
                    } else {
                        let errorMsg = 'Upload failed with status ' + xhr.status;
                        if (xhr.status === 413) {
                            errorMsg = 'File is larger than the Supabase Storage limit for this bucket/project.';
                        }
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMsg = response.message;
                            }
                        } catch (e) {
                            // Use default error message
                        }
                        reject(new Error(errorMsg));
                    }
                });

                // Handle errors
                xhr.addEventListener('error', function() {
                    reject(new Error('Network error. Please check your connection and try again.'));
                });

                xhr.addEventListener('abort', function() {
                    reject(new Error('Upload cancelled.'));
                });

                // Handle timeout (5 minutes)
                xhr.timeout = 300000; // 5 minutes
                xhr.addEventListener('timeout', function() {
                    reject(new Error(
                        'Upload timed out. The file may be too large or your connection is slow.'));
                });

                // Open and send the request
                xhr.open('PUT', uploadUrl);
                const unsafeHeaders = ['host', 'content-length'];
                Object.entries(uploadHeaders).forEach(function([header, value]) {
                    if (unsafeHeaders.includes(header.toLowerCase())) {
                        return;
                    }
                    xhr.setRequestHeader(header, value);
                });
                if (!uploadHeaders['Content-Type'] && !uploadHeaders['content-type']) {
                    xhr.setRequestHeader('Content-Type', file.type);
                }
                xhr.send(file);
            });
        }

        // Reset form
        function resetForm() {
            selectedFile = null;
            fileInput.value = '';
            fileInfo.classList.remove('show');
            progressSection.classList.remove('show');
            errorAlert.classList.remove('show');
            successAlert.classList.remove('show');
            uploadBtn.disabled = false;
            uploadBtn.textContent = 'Upload to Supabase';
        }

        // Show error message
        function showError(message) {
            errorAlert.innerHTML = '❌ ' + message;
            errorAlert.classList.add('show');
            successAlert.classList.remove('show');
            progressSection.classList.remove('show');
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    </script>
</body>

</html>

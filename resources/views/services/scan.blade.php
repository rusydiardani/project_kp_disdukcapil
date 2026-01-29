@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 text-center">
        <h1 class="mb-4">Auto Scan KTP</h1>
        
        <div class="card shadow-lg mb-4">
            <div class="card-header bg-white py-3">
                <div id="statusIndicator" class="badge bg-secondary fs-5 px-4 py-2 rounded-pill">
                    Menghubungkan Kamera...
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Camera Viewfinder -->
                <div class="position-relative mb-3 bg-dark rounded overflow-hidden" style="height: 350px;">
                    <video id="video" autoplay playsinline class="w-100 h-100" style="object-fit: cover;"></video>
                    
                    <!-- Overlay Guide Box -->
                    <div id="guideBox" class="position-absolute top-50 start-50 translate-middle rounded transition-all"
                         style="width: 85%; height: 25%; box-shadow: 0 0 0 9999px rgba(0,0,0,0.6); border: 4px solid #dc3545; transition: border-color 0.3s ease;">
                        
                         <!-- Scan Line Animation -->
                        <div id="scanLine" class="scan-line" style="display:none;"></div>
                    </div>

                    <!-- Overlay Message (For Success/Error feedback) -->
                    <div id="feedbackOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center d-none" style="z-index: 10;">
                        <div id="feedbackContent" class="text-white p-4 rounded text-center" style="background: rgba(0,0,0,0.8); max-width: 80%;">
                            <i id="feedbackIcon" class="bi bi-check-circle display-1 mb-3"></i>
                            <h3 id="feedbackTitle" class="fw-bold">Berhasil!</h3>
                            <p id="feedbackMessage" class="fs-5">Data telah diupdate.</p>
                        </div>
                    </div>

                    <!-- Instruction Text -->
                    <p id="instructionText" class="text-white position-absolute top-100 start-50 translate-middle-x mt-3 fw-bold text-shadow">
                        Arahkan kamera ke NIK KTP
                    </p>
                </div>

                <div class="d-flex justify-content-center gap-2 mb-4">
                    <button type="button" class="btn btn-outline-secondary" id="toggleCamera">
                        <i class="bi bi-arrow-repeat me-2"></i> Ganti Kamera
                    </button>
                </div>

                <!-- Hidden Canvas -->
                <canvas id="canvas" style="display:none;"></canvas>

                <!-- Debug/Result Input (Optional, kept for clarity) -->
                <div class="mb-2">
                    <input type="text" id="nikDebug" class="form-control text-center border-0 bg-transparent" placeholder="Status Deteksi..." readonly disabled>
                </div>

            </div>
        </div>
        
        <a href="{{ route('dashboard') }}" class="btn btn-link">Kembali ke Dashboard</a>
    </div>
</div>

<style>
    .text-shadow { text-shadow: 0 2px 4px rgba(0,0,0,0.8); }
    .transition-all { transition: all 0.3s ease; }
    
    .scan-line {
        position: absolute;
        width: 100%;
        height: 2px;
        background: #00ff00;
        top: 0;
        animation: scanMove 1.5s infinite linear;
        box-shadow: 0 0 4px #00ff00;
    }

    @keyframes scanMove {
        0% { top: 0; opacity: 0; }
        5% { opacity: 1; }
        95% { opacity: 1; }
        100% { top: 100%; opacity: 0; }
    }
</style>

<!-- Tesseract.js CDN -->
<script src='https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js'></script>

<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const nikDebug = document.getElementById('nikDebug');
    
    const guideBox = document.getElementById('guideBox');
    const scanLine = document.getElementById('scanLine');
    const statusIndicator = document.getElementById('statusIndicator');
    const instructionText = document.getElementById('instructionText');
    
    const feedbackOverlay = document.getElementById('feedbackOverlay');
    const feedbackIcon = document.getElementById('feedbackIcon');
    const feedbackTitle = document.getElementById('feedbackTitle');
    const feedbackMessage = document.getElementById('feedbackMessage');
    const feedbackContent = document.getElementById('feedbackContent');

    const toggleCameraBtn = document.getElementById('toggleCamera');

    let currentStream = null;
    let usingFrontCamera = false;
    let isProcessing = false; // Flag to stop scanning while validtig
    let worker = null;
    let scanInterval = null;

    // 1. Initialize Camera
    async function startCamera() {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
        }

        const constraints = {
            video: {
                facingMode: usingFrontCamera ? "user" : "environment",
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        };

        try {
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            video.srcObject = stream;
            currentStream = stream;
            // Wait for video to play before scanning
            video.onloadedmetadata = () => {
                video.play();
                startScanLoop();
            };
        } catch (err) {
            console.error("Error:", err);
            statusIndicator.className = 'badge bg-danger';
            statusIndicator.innerText = "Kamera Gagal Akses";
        }
    }

    // Initialize Tesseract
    async function initWorker() {
        statusIndicator.innerText = "Memuat OCR Engine...";
        worker = await Tesseract.createWorker('ind');
        statusIndicator.className = 'badge bg-primary';
        statusIndicator.innerText = "SIAP SCAN";
        scanLine.style.display = 'block'; // Show scan line
    }

    // Main Entry
    (async () => {
        await initWorker();
        await startCamera();
    })();

    toggleCameraBtn.addEventListener('click', () => {
        usingFrontCamera = !usingFrontCamera;
        startCamera();
    });

    // The continuous loop
    async function startScanLoop() {
        if (scanInterval) clearTimeout(scanInterval);
        
        const loop = async () => {
            if (!worker || isProcessing) {
                // If busy, check again soon
                scanInterval = setTimeout(loop, 500);
                return;
            }

            statusIndicator.innerText = "Mencari NIK...";
            statusIndicator.className = 'badge bg-info text-dark';
            
            // Capture
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Crop (ROI)
            const cropW = canvas.width * 0.85;
            const cropH = canvas.height * 0.25;
            const cropX = (canvas.width - cropW) / 2;
            const cropY = (canvas.height - cropH) / 2;
            
            const cropped = document.createElement('canvas');
            cropped.width = cropW;
            cropped.height = cropH;
            cropped.getContext('2d').drawImage(canvas, cropX, cropY, cropW, cropH, 0, 0, cropW, cropH);

            try {
                // Simplified OCR without complex preprocessing or params
                const ret = await worker.recognize(cropped.toDataURL('image/png'));
                const text = ret.data.text.replace(/[^0-9]/g, '');
                
                nikDebug.value = "Deteksi: " + text.substring(0, 16) + "..."; // Feedback visual kecil

                // Regex Validation
                const match = text.match(/(\d{16})/);
                if (match) {
                    const nik = match[0];
                    await validateNik(nik);
                } else {
                    // Not found, continue
                    scanInterval = setTimeout(loop, 800); 
                }
            } catch (e) {
                console.error(e);
                scanInterval = setTimeout(loop, 1000);
            }
        };

        loop();
    }

    async function validateNik(nik) {
        isProcessing = true; // Stop scanning
        scanLine.style.display = 'none';
        
        statusIndicator.className = 'badge bg-warning text-dark';
        statusIndicator.innerText = "Memvalidasi NIK...";
        guideBox.style.borderColor = "#ffc107"; // Yellow

        try {
            const response = await fetch("{{ route('services.process_scan') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ nik: nik })
            });

            const data = await response.json();

            if (response.ok && data.status === 'success') {
                showFeedback('success', 'SUKSES!', data.message);
                
                // Wait 3s then reset
                setTimeout(() => {
                    hideFeedback();
                    // Optional: prevent immediate re-scan of same ID?
                    isProcessing = false;
                    scanLine.style.display = 'block';
                    startScanLoop();
                }, 3000);

            } else {
                // Error case (Not found or already done)
                showFeedback('error', 'GAGAL!', data.message || 'NIK tidak valid.');
                
                // Wait 3s then resume
                setTimeout(() => {
                    hideFeedback();
                    isProcessing = false;
                    scanLine.style.display = 'block';
                    startScanLoop();
                }, 3000);
            }

        } catch (err) {
            console.error(err);
            showFeedback('error', 'ERROR', 'Terjadi kesalahan koneksi.');
             setTimeout(() => {
                hideFeedback();
                isProcessing = false;
                scanLine.style.display = 'block';
                startScanLoop();
            }, 3000);
        }
    }

    function showFeedback(type, title, message) {
        feedbackOverlay.classList.remove('d-none');
        feedbackTitle.innerText = title;
        feedbackMessage.innerText = message;
        
        if (type === 'success') {
            feedbackIcon.className = 'bi bi-check-circle display-1 mb-3 text-success';
            guideBox.style.borderColor = "#198754"; // Green
        } else {
            feedbackIcon.className = 'bi bi-x-circle display-1 mb-3 text-danger';
            guideBox.style.borderColor = "#dc3545"; // Red
        }
    }

    function hideFeedback() {
        feedbackOverlay.classList.add('d-none');
        guideBox.style.borderColor = "#dc3545"; // Reset to red
        nikDebug.value = "";
    }

</script>
@endsection
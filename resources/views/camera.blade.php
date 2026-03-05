<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Booth</title>
</head>

<body>
    @extends('layouts.app')

    @section('content')

    <div class="min-h-screen bg-[#4a7c59] flex flex-col items-center justify-center px-4 py-10"
        style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px),
                            linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px);
         background-size: 40px 40px;">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-7xl font-italic text-pink-300 tracking-tight"> Photobooth</h1>
            <p class="text-gray-400 text-sm mt-2 tracking-widest uppercase">Strike a pose & capture the moment</p>
        </div>

        <!-- Camera Box -->
        <div class="relative w-full max-w-3xl  rounded-2xl overflow-hidden shadow-2xl border-4 border-pink-200">

            <!-- Live Video Feed -->
            <video id="video" autoplay playsinline
                class="w-full block bg-black object-cover" style="aspect-ratio: 16/9;">
            </video>


            <!-- Countdown Display -->
            <div id="countdown"
                class="absolute inset-0 flex items-center justify-center text-9xl font-black text-white drop-shadow-lg hidden">
            </div>

            <!-- Flash Effect -->
            <div id="flash"
                class="absolute inset-0 bg-white opacity-0 pointer-events-none transition-opacity duration-150">
            </div>

            <!-- Hidden Canvas for capture -->
            <canvas id="canvas" class="hidden"></canvas>

        </div>

        <!-- Status Message -->
        <p id="status" class="mt-4 text-gray-400 text-sm tracking-wide h-5 text-center"></p>

        <!-- Buttons -->
        <div class="flex gap-4 mt-6">

            <!-- Capture Button -->
            <button id="captureBtn"
                onclick="startCountdown()"
                class="bg-yellow-400 hover:bg-yellow-300 text-gray-950 font-bold text-lg px-10 py-4 rounded-full shadow-lg transition-all duration-200 hover:scale-105 active:scale-95">
                <img src="images/photo-camera-photocamera-svgrepo-com.svg" alt="camera" class="w-20 h-auto">
            </button>

            <!-- Retake Button -->
            <button id="retakeBtn"
                onclick="retake()"
                class="hidden bg-gray-700 hover:bg-gray-600 text-white font-bold text-lg px-8 py-4 rounded-full shadow-lg transition-all duration-200 hover:scale-105 active:scale-95">
                Retake
            </button>

        </div>

        <!-- Preview Box (shown after capture) -->
        <div id="previewBox" class="hidden mt-8 text-center w-full max-w-lg">
            <p class="text-gray-400 text-xs uppercase tracking-widest mb-3">Your Photo</p>
            <img id="preview" src="" alt="Captured photo"
                class="w-full rounded-2xl border-4 border-yellow-400 shadow-2xl">
            <a id="downloadBtn" href="#" download="photobooth.jpg"
                class="inline-block mt-5 bg-green-500 hover:bg-green-400 text-white font-bold px-8 py-3 rounded-full shadow-lg transition-all duration-200 hover:scale-105">
                Download Photo
            </a>
        </div>

    </div>

    <script>
        // Start webcam
        async function startDefaultCamera() {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const cameras = devices.filter(d => d.kind === 'videoinput');

            // Skip virtual cameras like DroidCam
            const realCamera = cameras.find(cam =>
                !cam.label.toLowerCase().includes('droid') &&
                !cam.label.toLowerCase().includes('virtual') &&
                !cam.label.toLowerCase().includes('obs')
            );

            const deviceId = realCamera ? realCamera.deviceId : undefined;

            const stream = await navigator.mediaDevices.getUserMedia({
                video: deviceId ? {
                    deviceId: {
                        exact: deviceId
                    }
                } : true,
                audio: false
            });

            video.srcObject = stream;
        }

        startDefaultCamera();

        // Countdown then capture
        function startCountdown() {
            const btn = document.getElementById('captureBtn');
            const countdown = document.getElementById('countdown');

            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');

            let count = 3;
            countdown.textContent = count;
            countdown.classList.remove('hidden');
            setStatus('Get ready...');

            const timer = setInterval(() => {
                count--;
                if (count > 0) {
                    countdown.textContent = count;
                } else {
                    clearInterval(timer);
                    countdown.classList.add('hidden');
                    capturePhoto();
                }
            }, 1000);
        }

        // Capture photo from video to canvas
        function capturePhoto() {
            // Flash effect
            const flash = document.getElementById('flash');
            flash.style.opacity = '1';
            setTimeout(() => flash.style.opacity = '0', 150);

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            const photoData = canvas.toDataURL('image/jpeg', 0.9);

            // Show preview
            document.getElementById('preview').src = photoData;
            document.getElementById('previewBox').classList.remove('hidden');
            document.getElementById('retakeBtn').classList.remove('hidden');
            document.getElementById('captureBtn').classList.add('hidden');
            setStatus('Photo captured! Uploading...');

            // Upload to Laravel
            uploadPhoto(photoData);
        }

        // Send photo to PhotoController@store
        function uploadPhoto(photoData) {
            fetch('/upload', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        photo: photoData
                    })
                })
                .then(res => res.json())
                .then(data => {
                    setStatus('Saved! Ready to download.');
                    document.getElementById('downloadBtn').href = data.url;
                })
                .catch(() => setStatus('Upload failed. Please try again.'));
        }

        // Reset to camera
        function retake() {
            document.getElementById('previewBox').classList.add('hidden');
            document.getElementById('retakeBtn').classList.add('hidden');

            const btn = document.getElementById('captureBtn');
            btn.disabled = false;
            btn.classList.remove('hidden', 'opacity-50', 'cursor-not-allowed');
            setStatus('');
        }

        function setStatus(msg) {
            document.getElementById('status').textContent = msg;
        }
    </script>

    @endsection
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camera Roll</title>
</head>

<body>
    @extends('layouts.app')

    @section('content')

    <div class="min-h-screen bg-gray-950 flex flex-col items-center justify-center px-4 py-10">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-5xl font-black text-white tracking-tight">Your Photo!</h1>
            <p class="text-gray-400 text-sm mt-2 tracking-widest uppercase">Looking great — save or share it</p>
        </div>

        <!-- Photo Preview -->
        <div class="w-full max-w-lg">
            <img src="{{ request('url') }}" alt="Your photobooth photo"
                class="w-full rounded-2xl border-4 border-yellow-400 shadow-2xl">
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 mt-8 justify-center">

            <!-- Download -->
            <a href="{{ request('url') }}" download="photobooth.jpg"
                class="bg-green-500 hover:bg-green-400 text-white font-bold text-lg px-8 py-4 rounded-full shadow-lg transition-all duration-200 hover:scale-105 active:scale-95">
                ⬇️ Download Photo
            </a>

            <!-- Retake -->
            <a href="/"
                class="bg-gray-700 hover:bg-gray-600 text-white font-bold text-lg px-8 py-4 rounded-full shadow-lg transition-all duration-200 hover:scale-105 active:scale-95">
                📷 Take Another
            </a>

        </div>

        <!-- Share Link -->
        <div class="mt-8 w-full max-w-lg">
            <p class="text-gray-500 text-xs uppercase tracking-widest mb-2 text-center">Share Link</p>
            <div class="flex items-center gap-2 bg-gray-900 border border-gray-700 rounded-xl px-4 py-3">
                <input id="shareUrl" type="text" value="{{ request('url') }}" readonly
                    class="flex-1 bg-transparent text-gray-300 text-sm outline-none truncate">
                <button onclick="copyLink()"
                    class="text-yellow-400 hover:text-yellow-300 font-bold text-sm transition-colors whitespace-nowrap">
                    Copy
                </button>
            </div>
            <p id="copyMsg" class="text-green-400 text-xs text-center mt-2 hidden">✅ Link copied!</p>
        </div>

    </div>

    <script>
        function copyLink() {
            const url = document.getElementById('shareUrl').value;
            navigator.clipboard.writeText(url).then(() => {
                const msg = document.getElementById('copyMsg');
                msg.classList.remove('hidden');
                setTimeout(() => msg.classList.add('hidden'), 2000);
            });
        }
    </script>

    @endsection
</body>

</html>
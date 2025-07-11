<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Metro Maps</title>
    @vite('resources/css/app.css')
</head>
<body class="m-0 p-0 h-full font-sans overflow-hidden">
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 bg-cover bg-center bg-repeat p-5 max-sm:flex-col" 
         style="background-image: url('{{ asset('images/bg-pattern.png') }}');">
        
        <!-- Logos Container -->
        <div class="flex items-center justify-center gap-10 mb-10 max-sm:flex-col max-sm:gap-5">
            <!-- Metro Logo -->
            <img src="{{ asset('images/logokotametro.png') }}" alt="Metro Logo" class="block w-25 h-auto">
            
            <!-- Diskominfo Logo -->
            <img src="{{ asset('logodiskominfo.png') }}" alt="Diskominfo Kota Metro" class="block w-50 h-auto">
        </div>
        
        <!-- Content -->
        <div class="text-center max-w-2xl">
            <h1 class="text-3xl font-bold text-gray-800 mb-4 max-sm:text-xl">Selamat Datang di Metro Maps Kota Metro!</h1>
            <p class="text-lg text-gray-600 italic mt-0 max-sm:text-base">"Peta Cerdas untuk Menjelajahi Kota Metro"</p>
        </div>
    </div>
</body>
    <script>
        function enterApp() {
            // Set session before redirecting
            fetch('/set-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => {
                document.body.style.transition = 'opacity 0.5s ease-out';
                document.body.style.opacity = '0';
                setTimeout(() => {
                    window.location.href = '/map';
                }, 500);
            });
        }

        setTimeout(() => {
            enterApp();
        }, 3000);

        // Dots animation
        let loadingDots = 0;
        const button = document.querySelector('.enter-button');
        if (button) {
            const originalText = button.textContent;
            const loadingInterval = setInterval(() => {
                loadingDots = (loadingDots + 1) % 4;
                const dots = '.'.repeat(loadingDots);
                button.textContent = `Memuat Peta${dots}`;
            }, 500);
            setTimeout(() => clearInterval(loadingInterval), 3000);
        }
    </script>
</html>




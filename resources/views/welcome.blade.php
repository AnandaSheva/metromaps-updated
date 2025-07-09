<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metro Maps Kota Metro</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }
        
        .splash-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f5f5f5;
            background-image: url("{{ asset('images/bg-pattern.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: repeat;
            padding: 20px;
            
        }
        
        .logos-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .logo {
            display: block;
        }
        
        .metro-logo {
            width: 100px;
            height: auto;
        }
        
        .diskominfo-logo-splash {
            width: 200px;
            height: auto;
        }
        
        .content {
            text-align: center;
            max-width: 600px;
        }
        
        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 16px;
        }
        
        p {
            font-size: 18px;
            color: #555;
            font-style: italic;
            margin-top: 0;
        }
        
        @media (max-width: 600px) {
            .logos-container {
                flex-direction: column;
                gap: 20px;
            }
            
            h1 {
                font-size: 22px;
            }
            
            p {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="splash-container">
        <div class="logos-container">
            <!-- Metro Logo -->
            <img src="{{ asset('images/logokotametro.png') }}" alt="Metro Logo" class="logo metro-logo">
            
            <!-- Diskominfo Logo -->
            <img src="{{ asset('logodiskominfo.png') }}" alt="Diskominfo Kota Metro" class="logo diskominfo-logo-splash">
        </div>
        
        <div class="content">
            <h1>Selamat Datang di Metro Maps Kota Metro!</h1>
            <p>"Peta Cerdas untuk Menjelajahi Kota Metro"</p>
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




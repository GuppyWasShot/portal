<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Portal TPL'; ?> - SV IPB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Header Navigasi -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center">
                    <img src="../includes/logo_tpl.png" alt="Logo TPL" class="h-10 w-10 mr-3">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Portal TPL</h1>
                        <p class="text-xs text-gray-500">Sekolah Vokasi IPB</p>
                    </div>
                </div>
                
                <!-- Navigation Menu -->
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="text-gray-700 hover:text-indigo-600 transition">
                        Beranda
                    </a>
                    <a href="galeri.php" class="text-gray-700 hover:text-indigo-600 transition">
                        Galeri Karya
                    </a>
                    <a href="tentang.php" class="text-gray-700 hover:text-indigo-600 transition">
                        Tentang TPL
                    </a>
                    <a href="../admin/login.php" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition text-sm">
                        Login Admin
                    </a>
                </nav>
                
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200">
            <div class="px-4 py-3 space-y-2">
                <a href="index.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    Beranda
                </a>
                <a href="galeri.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    Galeri Karya
                </a>
                <a href="tentang.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    Tentang TPL
                </a>
                <a href="../admin/login.php" class="block px-4 py-2 bg-indigo-600 text-white rounded-lg text-center">
                    Login Admin
                </a>
            </div>
        </div>
    </header>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
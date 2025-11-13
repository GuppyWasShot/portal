<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?> - Portal TPL</title>
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
        
        /* Sidebar animations */
        @media (max-width: 768px) {
            .sidebar-overlay {
                transition: opacity 0.3s ease;
            }
            .sidebar {
                transition: transform 0.3s ease;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    
    <div class="flex h-screen overflow-hidden">
        
        <!-- Mobile Sidebar Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden sidebar-overlay" onclick="toggleSidebar()"></div>
        
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed md:static inset-y-0 left-0 transform -translate-x-full md:translate-x-0 w-64 bg-indigo-900 text-white flex flex-col z-50 sidebar">
            
            <!-- Logo & Header -->
            <div class="p-6 border-b border-indigo-800 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Portal TPL</h1>
                    <p class="text-indigo-300 text-sm mt-1">Admin Panel</p>
                </div>
                <!-- Mobile Close Button -->
                <button onclick="toggleSidebar()" class="md:hidden text-white hover:text-indigo-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="index.php" class="flex items-center px-4 py-3 <?php echo $current_page == 'index' ? 'bg-indigo-800' : 'hover:bg-indigo-800'; ?> rounded-lg transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="kelola_karya.php" class="flex items-center px-4 py-3 <?php echo $current_page == 'kelola_karya' || $current_page == 'form_tambah_karya' || $current_page == 'form_edit_karya' ? 'bg-indigo-800' : 'hover:bg-indigo-800'; ?> rounded-lg transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Kelola Karya</span>
                </a>
                
                <!-- Link ke Public Site -->
                <div class="pt-4 mt-4 border-t border-indigo-800">
                    <a href="../public/index.php" target="_blank" class="flex items-center px-4 py-3 hover:bg-indigo-800 rounded-lg transition text-indigo-300">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span>Lihat Situs Publik</span>
                    </a>
                </div>
            </nav>
            
            <!-- User Profile & Logout -->
            <div class="p-4 border-t border-indigo-800">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-indigo-700 rounded-full flex items-center justify-center">
                        <span class="text-lg font-bold"><?php echo strtoupper(substr($_SESSION['admin_username'], 0, 1)); ?></span>
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="font-medium truncate"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
                        <p class="text-xs text-indigo-300">Administrator</p>
                    </div>
                </div>
                <a href="logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?')" 
                   class="flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition w-full">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Mobile Header -->
            <header class="md:hidden bg-white shadow-sm p-4 flex items-center justify-between">
                <div class="flex items-center">
                    <button onclick="toggleSidebar()" class="text-gray-600 hover:text-gray-900 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="text-lg font-bold text-gray-800">Portal TPL</h1>
                </div>
                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                    <span class="text-sm font-bold text-white"><?php echo strtoupper(substr($_SESSION['admin_username'], 0, 1)); ?></span>
                </div>
            </header>
            
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto">
                <?php // Content will be inserted here ?>
<x-layout>

    <body class="bg-gray-100 font-sans antialiased">
        <div class="flex min-h-screen w-full">

            <!-- Sidebar -->
            <aside class="w-55 bg-gray-300 shadow-xl rounded-lg fixed left-3 top-22 bottom-5 p-6">
                <div class="px-6 py-6 text-center border-b border-red-400">
                    <img src="https://picsum.photos/100" alt="Logo" class="rounded-full mb-4 mx-auto shadow-md">
                    <h2 class="text-xl font-semibold">Admin Panel</h2>
                </div>

                <nav class="mt-6">
                    <ul class="space-y-1 text-sm w-full">
                        <!-- Sidebar links -->
                        @php
                            $position = Auth::user()->firefighter?->position?->position_name;
                        @endphp
                        <li
                            class="flex items-center gap-2 py-2 px-2 rounded w-full hover:bg-red-800 hover:text-white transition">
                            @if ($position === 'Super Admin')
                                <a href="{{ route('superadmin.dashboard') }}">
                                    🏠 Dashboard
                                </a>
                            @else
                                <a href="{{ route('admin.dashboard') }}">
                                    🏠 Dashboard
                                </a>
                            @endif
                        </li>
                        <li
                            class="flex items-center gap-2 py-2 px-4 rounded w-full hover:bg-red-800 hover:text-white transition">
                            @if ($position === 'Super Admin')
                                <a href="{{ route('superadmin.fire_reports') }}">
                                    📋 Fire Reports
                                </a>
                            @else
                                <a href="{{ route('admin.fire_reports') }}">
                                    📋 Fire Reports
                                </a>
                            @endif
                        </li>
                        <li
                            class="flex items-center gap-2 py-2 px-4 rounded w-full hover:bg-red-800 hover:text-white transition">
                            <a href="{{ route('admin.reports') }}">
                                📊 Reports and Analytics
                            </a>
                        </li>
                        <li
                            class="flex items-center gap-2 py-2 px-4 rounded w-full hover:bg-red-800 hover:text-white transition">
                            <a href="{{ route('admin.firefighters') }}">
                                👨‍🚒 Firefighters
                            </a>
                        </li>
                        <li
                            class="flex items-center gap-2 py-2 px-4 rounded w-full hover:bg-red-800 hover:text-white transitio">
                            <a href="{{ route('admin.equipment.list') }}">
                                🧰 Equipments
                            </a>
                        </li>
                        <li
                            class="flex items-center gap-2 py-2 px-4 rounded w-full hover:bg-red-800 hover:text-white transition">
                            <a href="{{ route('admin.teams') }}">
                                👥 Teams
                            </a>
                        </li>
                        <li
                            class="flex items-center gap-2 py-2 px-4 rounded w-full hover:bg-red-800 hover:text-white transition">
                            <a href="#">
                                🏛️ Fire Aid
                            </a>
                        </li>
                        <li
                            class="flex items-center gap-2 py-2 px-4 rounded w-full hover:bg-red-800 hover:text-white transition">
                            <a href="#">
                                ⚙️ Settings
                            </a>
                        </li>

                        <!-- Logout Button with Confirmation -->
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="w-full" id="logoutForm">
                                @csrf
                                <button type="button"
                                    class="flex items-center gap-2 py-2 px-4 rounded w-full hover:bg-red-800 hover:text-white transition"
                                    onclick="return confirmLogout(event)">
                                    🚪 Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </aside>
            <section>
                {{ $slot }}
            </section>

        </div>

        <!-- Custom Confirmation Modal -->
        <div id="confirmModal"
            class="fixed inset-0 flex justify-center items-center bg-gray-500 bg-opacity-50 z-50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                <h3 class="text-lg font-semibold text-center mb-4">Are you sure you want to log out?</h3>
                <div class="flex justify-between">
                    <button id="confirmBtn"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-400 active:bg-red-400 transition-all duration-150 ease-in-out">OK</button>
                    <button id="cancelBtn" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
                </div>
            </div>
        </div>

        <script>
            function confirmLogout(event) {
                // Prevent form submission
                // event.preventDefault();

                // Show the custom modal
                document.getElementById('confirmModal').classList.remove('hidden');
            }

            // Add event listeners for the modal buttons
            document.getElementById('cancelBtn').addEventListener('click', function() {
                document.getElementById('confirmModal').classList.add('hidden'); // Hide the modal
            });

            document.getElementById('confirmBtn').addEventListener('click', function() {
                // Submit the logout form when confirmed
                document.getElementById('logoutForm').submit(); // This submits the logout form
            });
        </script>
    </body>
</x-layout>

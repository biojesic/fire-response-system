<x-layout>

    <body class="bg-gray-100 font-sans antialiased">
        <div class="flex min-h-screen w-full">

            @php
                $position = Auth::user()->firefighter?->position?->position_name;
            @endphp

            <aside
                class="w-45 bg-gray-300 shadow-xl rounded-lg fixed left-3 top-22 bottom-5 p-3 overflow-y-auto no-scrollbar">

                <div class="px-6 py-6 text-center border-b border-red-400">
                    {{-- <img src="{{ asset('storage/user_images/KRgaIzD9VlqEHLr9rTUGZqbPQSd3usb4ct1OhQQ0.jpg') }}"
                        alt="Logo" class="rounded-full mb-4 mx-auto shadow-md"> --}}
                    <h2 class="text-md font-semibold">Admin Panel</h2>
                </div>

                {{-- @if ($position === 'Provincial Director' || $position === 'Super Admin') --}}
                <nav class="text-sm w-full">
                    <ul class="space-y-1">
                        {{-- FIRE RESPONSE TAB --}}
                        @if (in_array($position, ['Provincial Director', 'Radio Operator']))
                            <li class="text-center">
                                <a href="{{ route('superadmin.fire.response') }}"
                                    class="flex items-center gap-2 py-2 px-2 rounded hover:bg-red-800 hover:text-white transition font-bold text-gray-700 px-2 mt-4 ">
                                    Fire Response
                                </a>
                            </li>
                        @endif

                        {{-- DASHBOARD TAB --}}
                        @if (in_array($position, ['Provincial Director', 'Super Admin', 'Admin']))
                            <li class="text-center">
                                <a href="{{ route('superadmin.dashboard') }}"
                                    class="flex items-center gap-2 py-2 px-2 rounded hover:bg-red-800 hover:text-white transition font-bold text-gray-700">
                                    Dashboard
                                </a>
                            </li>
                        @endif

                        {{-- RESOURCES --}}
                        <li x-data="{ open: false }" class="font-bold text-gray-700 mt-2">
                            <button @click="open = !open"
                                class="w-full text-left flex items-center justify-between py-2 px-2 rounded hover:bg-red-800 hover:text-white transition">
                                <span class="flex items-center gap-2">
                                    Resources
                                </span>
                                <span x-text="open ? '▲' : '▼'"></span>
                            </button>
                            <ul x-show="open" x-transition.duration.200ms class="pl-4 space-y-1 mt-2">
                                <li>
                                    <a href="#"
                                        class="flex items-center gap-2 py-2 px-2 rounded hover:bg-red-800 hover:text-white transition">
                                        Fire Stations
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.firefighters') }}"
                                        class="flex items-center gap-2 py-2 px-2 rounded hover:bg-red-800 hover:text-white transition">
                                        Firefighters
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.teams') }}"
                                        class="flex items-center gap-2 py-2 px-2 rounded hover:bg-red-800 hover:text-white transition">
                                        Teams
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.equipment.list') }}"
                                        class="flex items-center gap-2 py-2 px-2 rounded hover:bg-red-800 hover:text-white transition">
                                        Equipment
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- USER MANAGEMENT --}}
                        <li x-data="{ open: false }" class="font-bold text-gray-700">
                            <button @click="open = !open"
                                class="w-full text-left flex items-center justify-between py-2 px-2 rounded hover:bg-red-800 hover:text-white transition">
                                <span class="flex items-center gap-2">
                                    User Management
                                </span>
                                <span x-text="open ? '▲' : '▼'"></span>
                            </button>
                            <ul x-show="open" x-transition.duration.200ms class="pl-4 space-y-1 mt-2">
                                <li>
                                    <a href="{{ route('civilians.index') }}"
                                        class="flex items-center gap-2 py-2 px-2 rounded hover:bg-red-800 hover:text-white transition">
                                        Civilians
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('barangay.index') }}"
                                        class="flex items-center gap-2 py-2 px-2 rounded hover:bg-red-800 hover:text-white transition">
                                        Barangays
                                    </a>
                                </li>
                            </ul>
                        </li>


                        {{-- REPORTS & INSIGHTS --}}
                        <li x-data="{ open: false }" class="font-bold text-gray-700">
                            <button @click="open = !open"
                                class="w-full text-left flex items-center justify-between py-2 px-2 rounded hover:bg-red-800 hover:text-white transition">
                                <span class="flex items-center gap-2">
                                    Reports & Insights
                                </span>
                                <span x-text="open ? '▲' : '▼'"></span>
                            </button>
                            <ul x-show="open" x-transition.duration.200ms class="pl-4 space-y-1 mt-2">
                                <li>
                                    <a href="{{ route('superadmin.fire_reports') }}"
                                        class="flex items-center gap-2 py-2 px-2 rounded hover:bg-red-800 hover:text-white transition">
                                        Fire Reports
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.reports') }}"
                                        class="flex items-center gap-2 py-2 px-2 rounded hover:bg-red-800 hover:text-white transition">
                                        Analytics
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- SETTINGS --}}
                        <li x-data="{ open: false }" class="font-bold text-gray-700">
                            <button @click="open = !open"
                                class="w-full text-left flex items-center justify-between py-2 px-2 rounded hover:bg-red-800 hover:text-white transition">
                                <span class="flex items-center gap-2">
                                    Settings
                                </span>
                                <span x-text="open ? '▲' : '▼'"></span>
                            </button>
                            <ul x-show="open" x-transition.duration.200ms class="pl-4 space-y-1 mt-2">
                                <li>
                                    <a href="#"
                                        class="flex items-center gap-2 py-2 px-2 rounded hover:bg-red-800 hover:text-white transition">
                                        System Settings
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                                        @csrf
                                        <button type="button"
                                            class="flex items-center gap-2 py-2 px-2 rounded w-full hover:bg-red-800 hover:text-white transition"
                                            onclick="return confirmLogout(event)">
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </nav>
                {{-- @endif --}}
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

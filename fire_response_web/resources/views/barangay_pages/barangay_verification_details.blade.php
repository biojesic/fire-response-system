<x-dashboard>
    @section('title', 'Fire Emergency - BFP')
    <div class="container w-255 pt-6 ml-21">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Barangay Details</h1>
        </header>

        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="mb-4">
                <label class="font-semibold text-gray-700">Name:</label>
                <p class="text-gray-800">{{ $barangay->barangay_name }}</p>
            </div>

            <div class="mb-4">
                <label class="font-semibold text-gray-700">Barangay Hall Address:</label>
                <p class="text-gray-800">{{ $barangay->barangay_hall_address }}</p>
            </div>

            <div class="mb-4">
                <label class="font-semibold text-gray-700">Contact Number:</label>
                <p class="text-gray-800">{{ $barangay->contact_number }}</p>
            </div>

            <div class="mb-4">
                <label class="font-semibold text-gray-700">Proof of Legitimacy:</label>
                <div class="mb-2 mt-3">
                    @if ($barangay->barangay_legitimacy_proof)
                        <!-- Clickable thumbnail with modal -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = true" class="focus:outline-none group">
                                <img src="{{ asset('storage/' . $barangay->barangay_legitimacy_proof) }}"
                                    alt="Barangay Legitimacy Proof"
                                    class="w-32 h-32 object-cover cursor-pointer group-hover:opacity-75 transition-opacity rounded border border-gray-200">
                                <div
                                    class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">Click to
                                        view</span>
                                </div>
                            </button>

                            <!-- Image Modal -->
                            <div x-show="open" x-transition
                                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75"
                                @click.away="open = false" @keydown.escape.window="open = false">
                                <div class="relative bg-white rounded-lg max-w-4xl max-h-[70vh] overflow-auto">
                                    <div class="sticky top-0 bg-white p-4 border-b flex justify-between items-center">
                                        <h3 class="text-lg font-semibold">Proof of Legitimacy</h3>
                                        <button @click="open = false" class="text-gray-500 hover:text-gray-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="p-4">
                                        <img src="{{ asset('storage/' . $barangay->barangay_legitimacy_proof) }}"
                                            alt="Barangay Legitimacy Proof - Full View"
                                            class="max-w-full max-h-[70vh] mx-auto object-contain">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Display when no image exists -->
                        <div
                            class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-500">
                            <h4 class="text-center">No image available</h4>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Approve Form -->
            <form id="approveForm" action="{{ route('barangay.approve', $barangay->id) }}" method="POST">
                @csrf
                <button type="button" class="bg-green-500 text-white py-2 px-4 rounded"
                    onclick="openApproveModal()">Approve</button>
            </form>

            <!-- Reject Form -->
            <form id="rejectForm" action="" method="POST" class="mt-4">
                @csrf
                <label for="rejection_reason" class="font-semibold text-gray-700">Rejection Reason:</label>
                <textarea name="rejection_reason" id="rejection_reason" rows="3"
                    class="w-full p-2 border border-gray-300 rounded mt-2" placeholder="Enter reason..."></textarea>
                <button type="button" class="bg-red-500 text-white py-2 px-4 rounded mt-2"
                    onclick="openRejectModal()">Reject</button>
            </form>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-xl font-bold mb-4">Confirm Approval</h3>
            <p class="mb-4">Are you sure you want to approve this user?</p>
            <div class="flex justify-end">
                <button type="button" class="bg-gray-500 text-white py-2 px-4 rounded mr-2"
                    onclick="closeModal('approveModal')">Cancel</button>
                <button type="button" class="bg-green-500 text-white py-2 px-4 rounded"
                    onclick="submitApproveForm()">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h3 class="text-xl font-bold mb-4">Confirm Rejection</h3>
            <p class="mb-4">Are you sure you want to reject this user?</p>
            <div class="flex justify-end">
                <button type="button" class="bg-gray-500 text-white py-2 px-4 rounded mr-2"
                    onclick="closeModal('rejectModal')">Cancel</button>
                <button type="button" class="bg-red-500 text-white py-2 px-4 rounded"
                    onclick="submitRejectForm()">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        // Open the approve modal
        function openApproveModal() {
            document.getElementById('approveModal').classList.remove('hidden');
        }

        // Open the reject modal
        function openRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        // Close the modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Submit the approve form
        function submitApproveForm() {
            document.getElementById('approveForm').submit();
        }

        // Submit the reject form
        function submitRejectForm() {
            document.getElementById('rejectForm').submit();
        }
    </script>
</x-dashboard>

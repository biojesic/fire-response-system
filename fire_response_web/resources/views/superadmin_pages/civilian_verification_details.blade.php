<x-dashboard>
    @section('title', 'Fire Emergency - BFP')
    <div class="container w-255 pt-6 ml-21">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <h1 class="text-3xl font-bold text-gray-800">User Details</h1>
        </header>

        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="mb-4">
                <label class="font-semibold text-gray-700">First Name:</label>
                <p class="text-gray-800">{{ $civilianUser->userFirstName }}</p>
            </div>

            <div class="mb-4">
                <label class="font-semibold text-gray-700">Last Name:</label>
                <p class="text-gray-800">{{ $civilianUser->userLastName }}</p>
            </div>

            <div class="mb-4">
                <label class="font-semibold text-gray-700">Email:</label>
                <p class="text-gray-800">{{ $civilianUser->email }}</p>
            </div>

            <div class="mb-4">
                <label class="font-semibold text-gray-700">User Address:</label>
                <p class="text-gray-800">{{ $civilianUser->userAddress }}</p>
            </div>

            <div class="mb-4">
                <label class="font-semibold text-gray-700">Contact Number:</label>
                <p class="text-gray-800">{{ $civilianUser->userContactNumber }}</p>
            </div>

            <div class="mb-4">
                <label class="font-semibold text-gray-700">Birth Date:</label>
                <p class="text-gray-800">{{ $civilianUser->userBirthDate }}</p>
            </div>

            <div class="mb-4">
                <label class="font-semibold text-gray-700">Reapplication Count:</label>
                <p class="text-gray-800">{{ $civilianUser->reapplication_count }}</p>
            </div>

            <div class="mb-4">
                <label class="font-semibold text-gray-700">ID Image:</label>
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $civilianUser->id_image) }}" alt="ID Image"
                        class="w-32 h-32 object-cover">
                </div>
            </div>

            <div class="mb-4">
                <label class="font-semibold text-gray-700">User Image:</label>
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $civilianUser->profile_image) }}" alt="ID Image"
                        class="w-32 h-32 object-cover">
                </div>
            </div>

            <!-- Approve Form -->
            <form id="approveForm" action="{{ route('civilian.approve', $civilianUser->id) }}" method="POST">
                @csrf
                <button type="button" class="bg-green-500 text-white py-2 px-4 rounded"
                    onclick="openApproveModal()">Approve</button>
            </form>

            <!-- Reject Form -->
            <form id="rejectForm" action="{{ route('civilian.reject', $civilianUser->id) }}" method="POST"
                class="mt-4">
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

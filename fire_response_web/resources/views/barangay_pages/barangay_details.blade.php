 <x-dashboard>
     @section('title', 'Fire Emergency - BFP')
     <div class="container w-220 pt-6 ml-21" x-data="{ open: false }">
         <header class="flex justify-between items-center border-b pb-4 mb-6">
             <div class="flex items-center gap-4">
                 <!-- Back Button -->
                 <a href="{{ url()->previous() }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                             d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                     </svg>
                 </a>

                 <h1 class="text-3xl font-bold text-gray-800">Barangay Details</h1>
             </div>
         </header>

         <div class="bg-white p-6 rounded-lg shadow-lg">
             <div class="mb-4">
                 <label class="font-semibold text-gray-700">Barangay:</label>
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
                 <label class="font-semibold text-gray-700">City/Municipality:</label>
                 <p class="text-gray-800">{{ $barangay->lgu->name }}</p>
             </div>

             <div class="mb-4">
                 <label class="font-semibold text-gray-700">Proof of Legitimacy:</label>
                 <div class="mb-2 mt-3">
                     @if ($barangay->barangay_legitimacy_proof)
                         <!-- Clickable thumbnail (only shows if image exists) -->
                         <button @click="open = true" class="focus:outline-none">
                             <img src="{{ asset('storage/' . $barangay->barangay_legitimacy_proof) }}"
                                 alt="Barangay Legitimacy Proof"
                                 class="w-32 h-32 object-cover cursor-pointer hover:opacity-75 transition-opacity">
                         </button>
                     @else
                         <!-- Display when no image exists -->
                         <div
                             class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-500">
                             <h4 class="text-center">No image available</h4>
                         </div>
                     @endif
                 </div>
             </div>
         </div>

         <!-- Modal -->
         <div x-show="open" x-transition
             class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-xl"
             @click.away="open = false">
             <div class="bg-white rounded-lg max-w-md max-h-screen overflow-auto mt-10">
                 <div class="p-4">
                     <div class="flex justify-between items-center mb-4">
                         <h3 class="text-lg font-semibold">Barangay Legitimacy Proof</h3>
                         <button @click="open = false"
                             class="p-1 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                     d="M6 18L18 6M6 6l12 12" />
                             </svg>
                         </button>
                     </div>
                     <img src="{{ asset('storage/' . $barangay->barangay_legitimacy_proof) }}"
                         alt="Barangay Legitimacy Proof - Full View" class="max-w-full max-h-[80vh] mx-auto">
                 </div>
             </div>
         </div>
     </div>
 </x-dashboard>

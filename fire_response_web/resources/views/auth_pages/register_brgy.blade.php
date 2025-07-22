<x-layout>

    @section('title', 'Fire Emergency - Register Barangay')

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            position: relative;
            background-image: url('{{ asset('images/bfp-bg1.webp') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(246, 245, 245, 0.7);
            z-index: -1;
        }
    </style>

    <div class="container w-230 pt-6">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Create an Account for Barangay</h1>
            </div>
        </header>


        <div class="card">
            <h3 class ="mb-4">Barangay Details</h3>
            <form action="{{ route('barangay.register') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6"
                enctype="multipart/form-data">
                @csrf

                {{-- NAME --}}
                <div class="mb-4">
                    <label for="barangay_name">Barangay Name</label>
                    <input type="text" name="barangay_name" value="{{ old('barangay_name') }}"
                        class="input @error('barangay_name') ring-red-500 @enderror">
                    @error('barangay_name')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- BARANGAY HALL ADDRESS --}}
                <div class="mb-4">
                    <label for="barangay_hall_address">Address of Barangay Hall</label>
                    <input type="text" name="barangay_hall_address" value="{{ old('barangay_hall_address') }}"
                        class="input @error('barangay_hall_address') ring-red-500 @enderror">
                    @error('barangay_hall_address')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CONTACT NUMBER --}}
                <div class="mb-4">
                    <label for="contact_number">Barangay Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                        class="input @error('contact_number') ring-red-500 @enderror">
                    @error('contact_number')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SELECT CITY/MUNICIPALITY --}}
                <div class="mb-4">
                    <label for="lgu_id">City/Municipality</label>
                    <select name="lgu_id" id="lgu_id" class="input @error('lgu_id') ring-red-500 @enderror">
                        <option value="" disabled {{ old('lgu_id') ? '' : 'selected' }}>-- Select
                            City/Municipality --</option>
                        @foreach ($citiesAndMunicipalities as $city)
                            <option value="{{ $city->id }}" {{ old('lgu_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('lgu_id')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Barangay Legitimacy Proof Upload --}}
                <div class="mb-10">
                    <label for="barangay_legitimacy_proof">Barangay Legitimacy Proof</label>
                    <input type="file" name="barangay_legitimacy_proof" id="barangay_legitimacy_proof"
                        class="input @error('barangay_legitimacy_proof') ring-red-500 @enderror">
                    @error('barangay_legitimacy_proof')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <br>
                <h3 class ="">Details of Registrant</h3>
                <br>


                {{-- FIRST NAME --}}
                <div class="mb-4">
                    <label for="userFirstName">First Name</label>
                    <input type="text" name="userFirstName" value="{{ old('userFirstName') }}"
                        class="input @error('userFirstName') ring-red-500 @enderror">
                    @error('userFirstName')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- LAST NAME --}}
                <div class="mb-4">
                    <label for="userLastName">Last Name</label>
                    <input type="text" name="userLastName" value="{{ old('userLastName') }}"
                        class="input @error('userLastName') ring-red-500 @enderror">
                    @error('userLastName')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- EMAIL --}}
                <div class="mb-4">
                    <label for="email">Email</label>
                    <input type="text" name="email" value="{{ old('email') }}"
                        class="input @error('email') ring-red-500 @enderror">
                    @error('email')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CONTACT NUMBER --}}
                <div class="mb-4">
                    <label for="userContactNumber">Contact Number</label>
                    <input type="text" name="userContactNumber" value="{{ old('userContactNumber') }}"
                        class="input @error('userContactNumber') ring-red-500 @enderror">
                    @error('userContactNumber')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Registrant Verification ID --}}
                <div class="mb-10">
                    <label for="id_image">Valid ID</label>
                    <input type="file" name="id_image" id="id_image"
                        class="input @error('id_image') ring-red-500 @enderror">
                    @error('id_image')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>


                {{-- SUBMIT BUTTON --}}
                <div class="col-span-2 w-110 flex justify-center ml-55">
                    <button class="btn">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>

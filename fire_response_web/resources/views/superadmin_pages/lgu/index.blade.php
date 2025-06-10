<x-dashboard>
    @section('title', 'Fire Emergency - BFP')

    <div class="container w-235 pt-6 ml-21">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Cities and Municipalities</h1>
            </div>
        </header>

        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="text-left bg-gray-100">
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">Name</th>
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">Type</th>
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">Province</th>
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lgus as $lgu)
                    <tr class="border-b">
                        <td class="py-3 px-6 text-sm text-gray-700">{{ $lgu->name }}</td>
                        <td class="py-3 px-6 text-sm text-gray-700">{{ $lgu->type }}</td>
                        <td class="py-3 px-6 text-sm text-gray-700">{{ $lgu->province }}</td>

                        <td class="py-3 px-6 text-sm text-gray-700">
                            <a href="" class="text-blue-500 hover:text-blue-700">Edit</a> |
                            <form action="" method="POST" style="display:inline-block;"
                                onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</x-dashboard>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Custom Fields') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Manage Custom Fields</h3>
                        <a href="{{ route('custom-fields.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Add Field</a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 text-green-700 p-2 mb-4 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="min-w-full bg-white shadow rounded">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Type</th>
                                <th class="px-4 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fields as $field)
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $field->name }}</td>
                                    <td class="px-4 py-2">{{ $field->type }}</td>
                                    <td class="px-4 py-2">
                                        <form action="{{ route('custom-fields.destroy', $field) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-500 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
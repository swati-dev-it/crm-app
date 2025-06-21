<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Custom Field') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('custom-fields.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="block font-medium mb-1">Field Name</label>
                            <input type="text" name="name" class="w-full border border-gray-300 px-3 py-2 rounded">
                            @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium mb-1">Field Type</label>
                            <select name="type" class="w-full border border-gray-300 px-3 py-2 rounded">
                                <option value="text">Text</option>
                                <option value="number">Number</option>
                                <option value="date">Date</option>
                                <option value="email">Email</option>
                                <option value="textarea">Textarea</option>
                            </select>
                            @error('type')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
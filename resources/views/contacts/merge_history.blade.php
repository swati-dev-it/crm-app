<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Merged Contacts History</h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto">
        <table class="min-w-full bg-white shadow rounded">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2">Merged Contact</th>
                    <th class="px-4 py-2">Merged Into</th>
                    <th class="px-4 py-2">Merged On</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mergedContacts as $contact)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $contact->name }} ({{ $contact->email }})</td>
                        <td class="px-4 py-2">{{ $contact->mergedInto->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $contact->updated_at->format('d M Y, H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $mergedContacts->links() }}
    </div>
</x-app-layout>

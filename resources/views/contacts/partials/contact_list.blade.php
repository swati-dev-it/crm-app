<div class="overflow-x-auto">
    <table class="min-w-full bg-white rounded-lg shadow">
        <thead>
            <tr class="bg-gray-100 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">
                <th class="px-6 py-3">Image</th>
                <th class="px-6 py-3">Name</th>
                <th class="px-6 py-3">Email</th>
                <th class="px-6 py-3">Phone</th>
                <th class="px-6 py-3">Gender</th>
                <th class="px-6 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($contacts as $contact)
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if ($contact->profile_image)
                            <img src="{{ asset('storage/' . $contact->profile_image) }}" alt="{{ $contact->name }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">N/A</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $contact->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $contact->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $contact->phone }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm capitalize text-gray-600">{{ $contact->gender }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <button onclick="editContact({{ json_encode($contact) }})" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded mr-2">Edit</button>
                        <button onclick="deleteContact({{ $contact->id }})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Delete</button>

                        @php
                            $masterOptions = '';
                            foreach ($contacts as $c) {
                                if ($c->id !== $contact->id && $c->is_merged==false) {
                                    $masterOptions .= '<option value="' . e($c->id) . '">' . e($c->name) . ' (' . e($c->email) . ')</option>';
                                }
                            }
                        @endphp

                        @if ($contact->is_merged)
                            <span class="text-red-500">Merged into {{ $contact->mergedInto->name ?? 'N/A' }}</span>
                        @else
                             <button class="bg-purple-600 text-white px-3 py-1 rounded"
                                onclick='openMergeModal(`{!! addslashes($masterOptions) !!}`, @json($contact))'>
                                Merge
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No contacts found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal backdrop and form -->
<div id="contactModal" 
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 flex">
    
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl relative 
                transform transition-all duration-300 scale-95 opacity-0 translate-y-4 
                max-h-[90vh] overflow-y-auto">
        <button onclick="hideModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        <h2 class="text-xl font-semibold mb-4">Add / Edit Contact</h2>
        <form id="contactForm" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="id" id="contact_id">

            <div>
                <label class="block font-medium mb-1">Name</label>
                <input type="text" name="name" id="name" class="border border-gray-300 rounded-md px-3 py-2 w-full">
                <p id="error-name" class="text-sm text-red-600 mt-1"></p>
            </div>

            <div>
                <label class="block font-medium mb-1">Email</label>
                <input type="email" name="email" id="email" class="border border-gray-300 rounded-md px-3 py-2 w-full">
                <p id="error-email" class="text-sm text-red-600 mt-1"></p>
            </div>

            <div>
                <label class="block font-medium mb-1">Phone</label>
                <input type="text" name="phone" id="phone" class="border border-gray-300 rounded-md px-3 py-2 w-full">
                <p id="error-phone" class="text-sm text-red-600 mt-1"></p>
            </div>

            <div>
                <label class="block font-medium mb-1">Gender</label>
                <div class="flex gap-4">
                    <label><input type="radio" name="gender" value="male"> Male</label>
                    <label><input type="radio" name="gender" value="female"> Female</label>
                    <label><input type="radio" name="gender" value="other"> Other</label>
                </div>
                <p id="error-gender" class="text-sm text-red-600 mt-1"></p>
            </div>

            <div>
                <label class="block font-medium mb-1">Profile Image</label>
                <input type="file" name="profile_image" class="w-full">
                <p class="text-sm text-red-600 mt-1" id="error-profile_image"></p>
            </div>

            <div>
                <label class="block font-medium mb-1">Additional File</label>
                <input type="file" name="additional_file" class="w-full">
                <p class="text-sm text-red-600 mt-1" id="error-additional_file"></p>
            </div>

            <div id="customFields">
                <label class="block font-medium mb-1">Custome Fields</label>
                @foreach(\App\Models\CustomField::all() as $field)
                    <div class="mb-4">
                        <label class="block font-medium mb-1">{{ $field->name }}</label>
                        <input type="{{ $field->type }}" name="custom_fields[{{ $field->id }}]" class="border border-gray-300 rounded-md px-3 py-2 w-full">
                    </div>
                @endforeach
            </div>

            <div class="text-right">
                <button type="submit" id="saveContactBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Merge Contacts Modal -->
<div id="mergeModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-2xl relative transform transition-all duration-300 scale-95 opacity-0 translate-y-4" id="mergeModalBox">
        <button onclick="closeMergeModal()" class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-xl">&times;</button>
        <h2 class="text-xl font-semibold mb-4">Merge Contacts</h2>
        
        <form id="mergeForm" method="POST" action="{{ route('contacts.merge.submit') }}">
            @csrf
            <input type="hidden" name="secondary_contact_id" id="merge_secondary_contact_id">

            <div class="mb-4">
                <label class="block font-medium">Master Contact</label>
                <select name="master_contact_id" id="merge_master_contact_id" class="w-full border border-gray-300 rounded px-3 py-2">
                    <!-- Options injected dynamically -->
                </select>
            </div>

            <div class="mb-4">
                <label class="block font-medium">Secondary Contact</label>
                <input type="text" id="merge_secondary_contact_name" disabled class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100">
            </div>

            <div class="text-right">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Confirm Merge</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('mergeForm').addEventListener('submit', function (e) {
        const confirmed = confirm("Are you sure you want to merge these contacts? This action cannot be undone.");
        if (!confirmed) {
            e.preventDefault(); // Stop submission
        }
    });
    
    function showModal() {
        const modal = document.getElementById('contactModal');
        const modalBox = modal.querySelector('div.relative');
        modal.classList.remove('hidden');

        setTimeout(() => {
            modalBox.classList.remove('scale-95', 'opacity-0', 'translate-y-4');
            modalBox.classList.add('scale-100', 'opacity-100', 'translate-y-0');
        }, 10);
    }

    function hideModal() {
        const modal = document.getElementById('contactModal');
        const modalBox = modal.querySelector('div.relative');
        modalBox.classList.add('scale-95', 'opacity-0', 'translate-y-4');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function openMergeModal(masterOptionsHTML, secondaryContact) {
        $('#merge_master_contact_id').html(masterOptionsHTML);
        $('#merge_secondary_contact_name').val(secondaryContact.name);
        $('#merge_secondary_contact_id').val(secondaryContact.id);

        $('#mergeModal').removeClass('hidden').addClass('flex');

        setTimeout(() => {
            $('#mergeModalBox').removeClass('scale-95 opacity-0 translate-y-4')
                            .addClass('scale-100 opacity-100 translate-y-0');
        }, 10);
    }

    function closeMergeModal() {
        $('#mergeModalBox').addClass('scale-95 opacity-0 translate-y-4');
        setTimeout(() => {
            $('#mergeModal').addClass('hidden').removeClass('flex');
        }, 300);
    }
</script>
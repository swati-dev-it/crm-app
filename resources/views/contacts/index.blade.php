<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contacts') }}
        </h2>
    </x-slot>
    
    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-xl rounded-xl p-6">
                @if (session('success'))
                    <div id="successMsg" class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                        <strong>Success!</strong> {{ session('success') }}
                    </div>

                    <script>
                        setTimeout(() => {
                            document.getElementById('successMsg').classList.add('hidden');
                        }, 3000);
                    </script>
                @endif
                <!-- Flash Success Message -->
                <div id="flash-message" class="hidden mb-4">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        <strong class="font-bold">Success!</strong>
                        <span id="flash-text" class="block sm:inline"></span>
                    </div>
                </div>
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800">Contacts</h2>
                    <button id="createContactBtn" class="bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg">
                        Add New Contact
                    </button>
                </div>
                <!-- Filter Form -->
                <div class="flex flex-wrap gap-4 mb-6">
                    <input type="text" id="filter-name" placeholder="Name" class="border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-lg p-2 w-48">
                    <input type="text" id="filter-email" placeholder="Email" class="border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-lg p-2 w-48">
                    <select id="filter-gender" class="border-gray-300 focus:ring-blue-500 focus:border-blue-500 rounded-lg p-2 w-48">
                        <option value="">All Genders</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    <button id="apply-filters" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg">Apply Filter</button>
                    <button id="reset-filters" class="bg-gray-400 hover:bg-gray-500 text-white font-medium px-4 py-2 rounded-lg">   
                        Reset Filter
                    </button>
                    
                </div>
                <div class="text-right">
                        <a href="{{ route('contacts.mergeHistory') }}" class="text-blue-600 hover:underline">View Merge History</a>
                </div>
                <!-- Contact List -->
                <div id="contacts-list">
                    @include('contacts.partials.contact_list', ['contacts' => $contacts])
                </div>

                <!-- Contact Modal -->
                <div id="contactModal" class="hidden bg-gray-100 p-6 rounded-lg shadow-lg mt-8 border border-gray-300">
                    <form id="contactForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="contact_id">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold">Name</label>
                                <input type="text" name="name" id="name" class="mt-1 border-gray-300 rounded-md w-full">
                                <p class="text-sm text-red-600 mt-1" id="error-name"></p>
                            </div>

                            <div>
                                <label class="block font-semibold">Email</label>
                                <input type="email" name="email" id="email" class="mt-1 border-gray-300 rounded-md w-full">
                                <p class="text-sm text-red-600 mt-1" id="error-email"></p>
                            </div>

                            <div>
                                <label class="block font-semibold">Phone</label>
                                <input type="text" name="phone" id="phone" class="mt-1 border-gray-300 rounded-md w-full">
                                <p class="text-sm text-red-600 mt-1" id="error-phone"></p>
                            </div>

                            <div>
                                <label class="block font-semibold">Gender</label>
                                <div class="flex items-center gap-4 mt-1">
                                    <label><input type="radio" name="gender" value="male"> Male</label>
                                    <label><input type="radio" name="gender" value="female"> Female</label>
                                    <label><input type="radio" name="gender" value="other"> Other</label>
                                </div>
                                <p class="text-sm text-red-600 mt-1" id="error-gender"></p>
                            </div>

                            <div>
                                <label class="block font-semibold">Profile Image</label>
                                <input type="file" name="profile_image" class="mt-1">
                                <p class="text-sm text-red-600 mt-1" id="error-profile_image"></p>
                            </div>

                            <div>
                                <label class="block font-semibold">Additional File</label>
                                <input type="file" name="additional_file" class="mt-1">
                                <p class="text-sm text-red-600 mt-1" id="error-additional_file"></p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h4 class="font-semibold mb-2">Custom Fields</h4>
                            <div id="customFields" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach(\App\Models\CustomField::all() as $field)
                                    <div>
                                        <label>{{ $field->name }}</label>
                                        <input type="{{ $field->type }}" name="custom_fields[{{ $field->id }}]" class="border-gray-300 rounded-md w-full">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 text-right">
                            <button type="submit" id="saveContactBtn" class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-2 rounded-lg">Save Contact</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {

             $('#createContactBtn').on('click', function () {
                $('#contactForm')[0].reset();
                $('#contact_id').val('');
                showModal();
            });

            $('.close-modal').on('click', function () {
                $('#contactModal').addClass('hidden');
            });

            function fetchContacts() {
                const name = $('#filter-name').val();
                const email = $('#filter-email').val();
                const gender = $('#filter-gender').val();

                $.get('/contacts/filter', { name, email, gender }, function (html) {
                    $('#contacts-list').html(html);
                });
            }

            $('#apply-filters').on('click', function () {
                fetchContacts();
            });

            $('#reset-filters').on('click', function () {
                $('#filter-name').val('');
                $('#filter-email').val('');
                $('#filter-gender').val('');
                $('#apply-filters').trigger('click');
            });

            $('#contactForm').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData(this);
                var id = $('#contact_id').val();
                var url = id ? '/contacts/' + id : '/contacts';
                var type = 'POST';

                if (id) formData.append('_method', 'PUT');

                $.ajax({
                    url: url,
                    type: type,
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    success: function (res) {
                        $('#flash-text').text(res.message);
                        $('#flash-message').removeClass('hidden');

                        // optionally auto-hide after 3 seconds
                        setTimeout(() => {
                            $('#flash-message').addClass('hidden');
                        }, 3000);
                        $('#contactModal').addClass('hidden');
                        fetchContacts();
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON.errors;
                        // Clear previous errors
                        $('#contactForm p.text-red-600').text('');

                        for (let field in errors) {
                            $('#error-' + field).text(errors[field][0]);
                        }
                    }
                });
            });

            window.deleteContact = function (id) {
                if (confirm('Are you sure?')) {
                    $.ajax({
                        url: '/contacts/' + id,
                        type: 'POST',
                        data: { _method: 'DELETE', _token: $('input[name="_token"]').val() },
                        success: function (res) {
                            $('#flash-text').text(res.message);
                            $('#flash-message').removeClass('hidden');

                            // optionally auto-hide after 3 seconds
                            setTimeout(() => {
                                $('#flash-message').addClass('hidden');
                            }, 3000);
                            fetchContacts();
                        }
                    });
                }
            }

            window.editContact = function (contact) {
                $('#contact_id').val(contact.id);
                $('#name').val(contact.name);
                $('#email').val(contact.email);
                $('#phone').val(contact.phone);
                $("input[name='gender'][value='" + contact.gender + "']").prop('checked', true);

                // Reset file inputs if needed
                $("input[name='profile_image']").val('');
                $("input[name='additional_file']").val('');

                // Show the modal
                showModal();
            }
        });
    </script>
</x-app-layout>
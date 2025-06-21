<?php

namespace App\Http\Controllers;
use App\Repositories\Interfaces\ContactRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\CustomField;
use App\Models\ContactCustomFieldValue;

class ContactController extends Controller
{
    public function __construct(protected ContactRepositoryInterface $contactRepository) {
        
    }

    public function index(Request $request) {
        $contacts = $this->contactRepository->all();

         // Generate master options HTML (used in merge modal)
        $masterOptions = '';
        foreach ($contacts as $contact) {
            if(!$contact->is_merged)
            {
                $masterOptions .= '<option value="' . $contact->id . '">' . e($contact->name) . ' (' . e($contact->email) . ')</option>';
            }
            
        }

        return view('contacts.index', compact('contacts', 'masterOptions'));
    }

    public function store(Request $request) {
        $data = $request->only(['name', 'email', 'phone', 'gender']);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|unique:contacts,email|email|max:255',
            'phone' => 'required|unique:contacts,phone|string|max:20',
            'gender' => 'required|in:male,female,other',
            'profile_image' => 'nullable|image|max:2048',
            'additional_file' => 'nullable|file|max:4096',
        ]);
        if ($request->hasFile('profile_image'))
            $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        if ($request->hasFile('additional_file'))
            $data['additional_file'] = $request->file('additional_file')->store('docs', 'public');

        $contact = $this->contactRepository->create($data);

        foreach ($request->custom_fields ?? [] as $field_id => $value) {
            $contact->customFieldValues()->create([
                'custom_field_id' => $field_id,
                'value' => $value,
            ]);
        }
        return response()->json(['message' => 'Contact created']);
    }

    public function update(Request $request, $id) {
        $contact = $this->contactRepository->find($id);
        $data = $request->only(['name', 'email', 'phone', 'gender']);
        if ($request->hasFile('profile_image'))
            $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        if ($request->hasFile('additional_file'))
            $data['additional_file'] = $request->file('additional_file')->store('docs', 'public');

        $this->contactRepository->update($id, $data);
        return response()->json(['message' => 'Contact updated']);
    }

    public function destroy($id) {
        $this->contactRepository->delete($id);
        return response()->json(['message' => 'Contact deleted']);
    }

    public function filter(Request $request) {
        $contacts = Contact::query()
            ->when($request->name, fn($q) => $q->where('name', 'like', "%{$request->name}%"))
            ->when($request->email, fn($q) => $q->where('email', 'like', "%{$request->email}%"))
            ->when($request->gender, fn($q) => $q->where('gender', $request->gender))
            ->where('is_merged', 0)
            ->orderBy('id', 'desc')
            ->get();

        return view('contacts.partials.contact_list', compact('contacts'));
    }

    public function mergeSubmit(Request $request)
    {
        $request->validate([
            'master_contact_id' => 'required|exists:contacts,id',
            'secondary_contact_id' => 'required|exists:contacts,id|different:master_contact_id',
        ]);

        $master = Contact::with('customFieldValues')->findOrFail($request->master_contact_id);
        $secondary = Contact::with('customFieldValues')->findOrFail($request->secondary_contact_id);

        // Merge emails and phones if different (extend logic as needed)
        if ($master->email !== $secondary->email) {
            $master->email .= ', ' . $secondary->email;
        }

        if ($master->phone !== $secondary->phone) {
            $master->phone .= ', ' . $secondary->phone;
        }

        // Merge custom fields
        $masterFields = $master->customFieldValues->keyBy('custom_field_id');
        foreach ($secondary->customFieldValues as $fieldValue) {
            if (!isset($masterFields[$fieldValue->custom_field_id])) {
                // Copy to master if not already present
                ContactCustomFieldValue::create([
                    'contact_id' => $master->id,
                    'custom_field_id' => $fieldValue->custom_field_id,
                    'value' => $fieldValue->value,
                ]);
            }
        }

        // Mark secondary contact as merged
        $secondary->update([
            'is_merged' => true,
            'merged_into_id' => $master->id,
        ]);

        $master->save();

        return redirect()->route('contacts')->with('success', 'Contacts merged successfully.');
    }

    public function mergeHistory()
    {
        $mergedContacts = Contact::whereNotNull('merged_into_id')->with('mergedInto')->latest()->paginate(10);
        return view('contacts.merge_history', compact('mergedContacts'));
    }
}

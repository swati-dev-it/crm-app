<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Repositories\Interfaces\ContactRepositoryInterface;

class ContactRepository implements ContactRepositoryInterface {
    public function all() {
        return Contact::with('customFieldValues.customField')->where('is_merged', 0)->orderBy('id', 'desc')->get();
    }

    public function find($id) {
        return Contact::with('customFieldValues.customField')->findOrFail($id);
    }

    public function create(array $data) {
        return Contact::create($data);
    }

    public function update($id, array $data) {
        $contact = Contact::findOrFail($id);
        $contact->update($data);
        return $contact;
    }

    public function delete($id) {
        return Contact::destroy($id);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function store(Request $request) {

    $data = array(
        "tes" => 'berhasil'
    );
    if ($request->hasFile('foto')) {
        $data['tes'] = $request->file('foto')->getClientOriginalName();
        $data['ya'] = $request->tes;
    }
    $data = json_encode($data);
    return $data;
    }

    public function all(Request $request) {

    }

    public function update() {

    }

    public function delete($id) {

    }
}

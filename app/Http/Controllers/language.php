<?php

namespace App\Http\Controllers;

use App\Models\languages;
use Illuminate\Http\Request;

class language extends Controller
{
    public function daftarLanguage(Request $request)
    {
        $cek = false;
        if ($request->has('search')) {
            $daftar = languages::where('language_name', 'LIKE', '%' . $request->search.'%')->paginate(10);
            $cek = true;
        } else {
            // jika tidak melakukan request
            $daftar = languages::paginate(10);
        }
        return view('daftarLanguages', compact('daftar'), [
            'cek' => $cek
        ]);
    }
}

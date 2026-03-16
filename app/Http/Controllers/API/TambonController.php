<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tambon;

class TambonController extends Controller
{
    public function getProvinces()
    {
        return Tambon::select('province')
            ->distinct()
            ->orderBy('province')
            ->get();
    }

    public function getAmphoes(Request $request)
    {
        $province = $request->province;

        return Tambon::select('amphoe')
            ->where('province', $province)
            ->distinct()
            ->orderBy('amphoe')
            ->get();
    }

    public function getTambons(Request $request)
    {
        return Tambon::select('tambon')
            ->where('province', $request->province)
            ->where('amphoe', $request->amphoe)
            ->distinct()
            ->orderBy('tambon')
            ->get();
    }

    public function getZipcodes(Request $request)
    {
        return Tambon::select('zipcode')
            ->where('province', $request->province)
            ->where('amphoe', $request->amphoe)
            ->where('tambon', $request->tambon)
            ->distinct()
            ->get();
    }
}

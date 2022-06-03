<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JasperReportController extends Controller
{
    public function index()
    {
        $categories = Auth::user()->categories;

        return view('jasperreport', compact('categories'));
    }

    public function getReport(Request $request)
    {
        $url = 'http://daw2-gcasas:2737C@51.68.224.27:8080/jasperserver/rest_v2/reports/daw2-gcasas/sintesis.pdf?email=' . Auth::user()->email;

        if (isset($request->categories) && $request->categories != null) {
            foreach ($request->categories as $category_name) {
                $url .= "&category_names=$category_name";
            }
        }

        try {
            $tempfile = file_get_contents($url);
            Storage::put("report.pdf", $tempfile);
            return response()->file(storage_path() . "/app/report.pdf");
        } catch (Exception $ex) {
            abort(404);
        }
    }
}

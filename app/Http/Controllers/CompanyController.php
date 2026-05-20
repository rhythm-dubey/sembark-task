<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $companies = Company::query()
            ->withCount(['users', 'shortUrls'])
            ->withSum('shortUrls as total_hits', 'hits')
            ->orderBy('name')
            ->get();

        return Inertia::render('companies/Index', [
            'companies' => $companies,
        ]);
    }
}

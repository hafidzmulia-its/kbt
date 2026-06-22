<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Services\OfficialTemplateCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function __construct(
        private readonly OfficialTemplateCatalogService $officialTemplateCatalogService,
    ) {
    }

    public function index(): View
    {
        $this->officialTemplateCatalogService->sync();

        return view('admin.templates.index', [
            'templates' => Template::latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:templates,code'],
            'category' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'is_premium' => ['sometimes', 'boolean'],
        ]);

        Template::create($data + ['is_premium' => $request->boolean('is_premium')]);

        return back()->with('status', 'Template disimpan.');
    }

    public function update(Request $request, Template $template): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'is_premium' => ['sometimes', 'boolean'],
        ]);

        $template->update($data + ['is_premium' => $request->boolean('is_premium')]);

        return back()->with('status', 'Template diperbarui.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MenuLocationController extends Controller
{
    public function edit(): View
    {
        $menus = Menu::query()
            ->orderBy('name')
            ->get();

        $assignments = MenuLocation::query()
            ->whereIn('location', array_keys(MenuLocation::DEFINITIONS))
            ->pluck('menu_id', 'location')
            ->all();

        return view('admin.menus.locations', [
            'menus' => $menus,
            'locationDefinitions' => MenuLocation::DEFINITIONS,
            'assignments' => $assignments,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [
            'assignments' => ['required', 'array'],
        ];

        foreach (array_keys(MenuLocation::DEFINITIONS) as $location) {
            $rules['assignments.'.$location] = ['nullable', 'integer', Rule::exists('menus', 'id')];
        }

        $validated = $request->validate($rules);
        $submittedAssignments = $validated['assignments'];

        foreach (array_keys(MenuLocation::DEFINITIONS) as $location) {
            $menuId = $submittedAssignments[$location] ?? null;

            if ($menuId === null) {
                MenuLocation::query()
                    ->where('location', $location)
                    ->delete();

                continue;
            }

            MenuLocation::query()->updateOrCreate(
                ['location' => $location],
                ['menu_id' => $menuId]
            );
        }

        return back()->with('success', 'Menu locations updated successfully.');
    }
}

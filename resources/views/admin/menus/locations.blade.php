@extends('layouts.admin')

@section('title', 'Menu Locations - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Menu Locations</h1>
            <p><a href="{{ route('admin.menus.index') }}">Back to navigation menus</a></p>
        </header>

        <form method="POST" action="{{ route('admin.menu-locations.update') }}" novalidate>
            @csrf
            @method('PATCH')

            @foreach ($locationDefinitions as $locationKey => $locationLabel)
                <div>
                    <label for="location_{{ $locationKey }}">{{ $locationLabel }} menu</label>
                    <select id="location_{{ $locationKey }}" name="assignments[{{ $locationKey }}]">
                        <option value="">None</option>
                        @foreach ($menus as $menu)
                            <option
                                value="{{ $menu->id }}"
                                @selected((string) old('assignments.'.$locationKey, $assignments[$locationKey] ?? '') === (string) $menu->id)
                            >
                                {{ $menu->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endforeach

            <button type="submit">Save locations</button>
        </form>
    </section>
@endsection

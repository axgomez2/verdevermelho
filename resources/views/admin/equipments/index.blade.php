@extends('layouts.admin')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Equipments</h1>
    <a href="{{ route('admin.equipments.create') }}" class="btn btn-primary mb-4">Add Equipment</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <table class="min-w-full bg-white">
        <thead>
            <tr>
                <th class="py-2">Name</th>
                <th class="py-2">Category</th>
                <th class="py-2">Product Type</th>
                <th class="py-2">Price</th>
                <th class="py-2">Stock</th>
                <th class="py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipments as $equipment)
                <tr>
                    <td class="py-2">{{ $equipment->name }}</td>
                    <td class="py-2">{{ $equipment->category->name }}</td>
                    <td class="py-2">{{ $equipment->productType->name }}</td>
                    <td class="py-2">{{ $equipment->price }}</td>
                    <td class="py-2">{{ $equipment->stock }}</td>
                    <td class="py-2">
                        <a href="{{ route('admin.equipments.edit', $equipment->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('admin.equipments.destroy', $equipment->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $equipments->links() }}
</div>
@endsection

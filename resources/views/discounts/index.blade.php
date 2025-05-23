@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Daftar Diskon</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('discounts.create') }}" class="btn btn-secondary">Tambah Diskon Baru</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Persentase</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($discounts as $discount)
                            <tr>
                                <td>{{ $discount->product->name }}</td>
                                <td>{{ $discount->percentage }}%</td>
                                <td>
                                    <a href="{{ route('discounts.edit', $discount) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('discounts.destroy', $discount) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus diskon ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $discounts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Tambah Diskon Baru</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('discounts.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('discounts.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="product_id" class="form-label">Produk</label>
                    <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                        <option value="">Pilih Produk</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="percentage" class="form-label">Persentase Diskon</label>
                    <input type="number" class="form-control @error('percentage') is-invalid @enderror" id="percentage" name="percentage" value="{{ old('percentage') }}" min="0" max="100" required>
                    @error('percentage')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
@endsection
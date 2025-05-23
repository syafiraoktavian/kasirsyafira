@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Struk Pembayaran</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('orders.create') }}" class="btn btn-primary">Pesanan Baru</a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="text-center mb-4">
                <h3>KASIR SYAFIRA</h3>
                <p>No. Order: #{{ $order->id }}</p>
                <p>Tanggal: {{ $order->created_at->format('d/m/Y H:i:s') }}</p>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td class="text-end">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="text-end"><strong>Diskon:</strong></td>
                                <td class="text-end">Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="text-end"><strong>Final:</strong></td>
                            <td class="text-end">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="text-center mt-4">
                <p>Metode Pembayaran: {{ ucfirst($order->payment_method) }}</p>
                <p class="mb-4">Terima Kasih Atas Kunjungan Anda</p>

                <a href="data:text/plain;charset=utf-8,{{ urlencode($receipt) }}"
                   download="receipt-{{ $order->id }}.txt"
                   class="btn btn-primary">
                    Download Struk
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

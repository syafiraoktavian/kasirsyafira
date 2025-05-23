@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Riwayat Pembelian</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('orders.create') }}" class="btn btn-primary">Buat Pesanan Baru</a>
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
                            <!-- <th>Tanggal</th> -->
                            <th>Total</th>
                            <th>Diskon</th>
                            <th>Harga Akhir</th>
                            <th>Metode Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <!-- <td>{{ $order->created_at->format('d/m/Y H:i') }}</td> -->
                                <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($order->final_amount, 0, ',', '.') }}</td>
                                <td>{{ ucfirst($order->payment_method) }}</td>
                                <td>
                                    <a href="{{ route('orders.receipt', $order) }}" class="btn btn-sm btn-info" target="_blank">
                                        <i class="fas fa-receipt"></i> Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Pemesanan Baru</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                @csrf
                <div id="items">
                    <div class="row mb-3 item">
                        <div class="col-md-5">
                            <select class="form-select product-select" name="items[0][product_id]" required>
                                <option value="">Pilih Produk</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                        data-price="{{ $product->price }}"
                                        data-stock="{{ $product->stock }}"
                                        data-discount="{{ $product->discount ? $product->discount->percentage : 0 }}">
                                        {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                                        @if($product->discount && $product->discount->is_active)
                                            (Diskon {{ $product->discount->percentage }}%)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control quantity" name="items[0][quantity]" placeholder="Jumlah" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control subtotal" readonly>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger remove-item" style="display: none;">×</button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-8 text-end">
                        <button type="button" class="btn btn-success" id="addItem">+ Tambah Item</button>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Metode Pembayaran</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" checked>
                            <label class="form-check-label" for="cash">Tunai</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="card" value="card">
                            <label class="form-check-label" for="card">Kartu</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Ringkasan</h5>
                                <div class="d-flex justify-content-between">
                                    <span>Total:</span>
                                    <span id="total">Rp 0</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Diskon:</span>
                                    <span id="discount">Rp 0</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Final:</strong>
                                    <strong id="final">Rp 0</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Buat Pesanan</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsContainer = document.getElementById('items');
    const addItemButton = document.getElementById('addItem');
    let itemCount = 1;

    function updateSubtotal(row) {
        const select = row.querySelector('.product-select');
        const quantity = row.querySelector('.quantity');
        const subtotal = row.querySelector('.subtotal');
        const option = select.options[select.selectedIndex];

        if (option.value && quantity.value) {
            const price = parseFloat(option.dataset.price);
            const qty = parseInt(quantity.value);
            const discount = parseFloat(option.dataset.discount);
            const total = price * qty;
            const discountAmount = total * (discount / 100);
            const final = total - discountAmount;

            subtotal.value = `Rp ${final.toLocaleString('id-ID')}`;
        } else {
            subtotal.value = '';
        }

        updateSummary();
    }

    function updateSummary() {
        let total = 0;
        let discount = 0;

        document.querySelectorAll('.item').forEach(row => {
            const select = row.querySelector('.product-select');
            const quantity = row.querySelector('.quantity');
            const option = select.options[select.selectedIndex];

            if (option.value && quantity.value) {
                const price = parseFloat(option.dataset.price);
                const qty = parseInt(quantity.value);
                const discountPercentage = parseFloat(option.dataset.discount);

                const itemTotal = price * qty;
                const itemDiscount = itemTotal * (discountPercentage / 100);

                total += itemTotal;
                discount += itemDiscount;
            }
        });

        const final = total - discount;

        document.getElementById('total').textContent = `Rp ${total.toLocaleString('id-ID')}`;
        document.getElementById('discount').textContent = `Rp ${discount.toLocaleString('id-ID')}`;
        document.getElementById('final').textContent = `Rp ${final.toLocaleString('id-ID')}`;
    }

    function addItem() {
        const template = document.querySelector('.item').cloneNode(true);
        template.querySelector('.product-select').name = `items[${itemCount}][product_id]`;
        template.querySelector('.quantity').name = `items[${itemCount}][quantity]`;
        template.querySelector('.product-select').value = '';
        template.querySelector('.quantity').value = '';
        template.querySelector('.subtotal').value = '';
        template.querySelector('.remove-item').style.display = 'block';

        itemsContainer.appendChild(template);
        itemCount++;

        updateSummary();
    }

    function removeItem(button) {
        button.closest('.item').remove();
        updateSummary();
    }

    // Event Listeners
    addItemButton.addEventListener('click', addItem);

    itemsContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select') || e.target.classList.contains('quantity')) {
            updateSubtotal(e.target.closest('.item'));
        }
    });

    itemsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            removeItem(e.target);
        }
    });
});
</script>
@endpush
@endsection

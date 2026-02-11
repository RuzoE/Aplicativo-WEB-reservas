@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    Recepción — Folio
</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="bg-white shadow rounded p-6">
        <div class="flex justify-between items-center mb-4">
                        <option>Tarjeta</option>
                        <option>Transferencia</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm">Monto</label>
                    <input name="amount" type="number" step="0.01" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div class="text-right">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Registrar</button>
                </div>
            </form>
        </div>
    </div>
    @if(session('status'))
        <div class="mt-4 p-3 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
    @endif
</div>
@endsection
@extends('layouts.app')
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Recepción — Folio</h2>
    @endsection

@section('content')

    <div class="p-6 space-y-6">
        @if(session('status'))
            <div class="bg-green-100 text-green-800 p-2 rounded">{{ session('status') }}</div>
        @endif

        <div>
            <h3 class="font-semibold">Resumen</h3>
            <p>Stay #{{ $stay->id }} — Hab {{ $stay->room_id }} — {{ optional($stay->guest)->first_name }} {{ optional($stay->guest)->last_name }}</p>
            @if($folio)
                <p>Folio: {{ $folio->number }} — Estado: {{ $folio->status }} — Balance: {{ number_format($folio->balance, 2) }} {{ $folio->currency }}</p>
            @else
                <p>No hay folio abierto.</p>
            @endif
        </div>

        @if($folio)
        <div class="grid grid-cols-2 gap-6">
            <div>
                <h4 class="font-semibold">Cargos</h4>
                <ul>
                    @foreach($folio->charges as $c)
                        <li>{{ $c->posted_at }} — {{ $c->source }}: {{ $c->description }} — {{ number_format($c->amount + ($c->tax ?? 0), 2) }}</li>
                    @endforeach
                </ul>

                <form method="POST" action="{{ route('reception.folio.charge', $stay->id) }}" class="space-y-2 mt-4">
                    @csrf
                    <input name="source" placeholder="Fuente" class="border rounded w-full" required />
                    <input name="description" placeholder="Descripción" class="border rounded w-full" required />
                    <input name="amount" type="number" step="0.01" placeholder="Monto" class="border rounded w-full" required />
                    <input name="tax" type="number" step="0.01" placeholder="Impuesto" class="border rounded w-full" />
                    <button class="px-3 py-1 bg-blue-600 text-white rounded">Agregar cargo</button>
                </form>
            </div>
            <div>
                <h4 class="font-semibold">Pagos</h4>
                <ul>
                    @foreach($folio->payments as $p)
                        <li>{{ $p->received_at }} — {{ $p->method }}: {{ number_format($p->amount, 2) }} {{ $p->currency }}</li>
                    @endforeach
                </ul>

                <form method="POST" action="{{ route('reception.folio.payment', $stay->id) }}" class="space-y-2 mt-4">
                    @csrf
                    <input name="method" placeholder="Método" class="border rounded w-full" required />
                    <input name="amount" type="number" step="0.01" placeholder="Monto" class="border rounded w-full" required />
                    <input name="currency" placeholder="Moneda (e.g., USD)" class="border rounded w-full" required />
                    <input name="external_ref" placeholder="Referencia externa" class="border rounded w-full" />
                    <button class="px-3 py-1 bg-green-600 text-white rounded">Registrar pago</button>
                </form>
            </div>
        </div>

        <form method="POST" action="{{ route('reception.checkout.store', $stay->id) }}" class="mt-6">
            @csrf
            <button class="px-4 py-2 bg-purple-600 text-white rounded">Completar check-out</button>
        </form>
        @endif
    </div>
@endsection


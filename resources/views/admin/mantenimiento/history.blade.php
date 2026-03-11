@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1>Historial de Mantenimiento</h1>
    <p>Aquí puedes ver el historial de órdenes de mantenimiento.</p>
</div>
@endsection

@if ($history->isEmpty())
    <div style="text-align: center; padding: 20px; color: #999;">
        <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.3; display: block; margin-bottom: 10px;"></i>
        <p>No hay registros de mantenimiento para esta habitación</p>
    </div>
@else
    <div style="display: flex; flex-direction: column; gap: 15px;">
        @foreach ($history as $order)
            <div style="border: 2px solid #eee; border-radius: 8px; padding: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <div>
                        <strong style="color: #333;">{{ ucfirst($order->description) }}</strong>
                    </div>
                    <span class="priority-badge priority-{{ $order->priority }}">
                        @if ($order->priority === 'urgente')
                            <i class="fas fa-star"></i> Urgente
                        @else
                            {{ ucfirst($order->priority) }}
                        @endif
                    </span>
                </div>

                <div style="color: #666; font-size: 0.9rem; margin-bottom: 10px;">
                    @if ($order->status === 'completada')
                        <span style="display: inline-block; background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                            <i class="fas fa-check-circle"></i> Completada
                        </span>
                    @else
                        <span style="display: inline-block; background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                            <i class="fas fa-hourglass-half"></i> {{ ucfirst($order->status) }}
                        </span>
                    @endif
                </div>

                @if ($order->notes)
                    <div style="background: #f9f9f9; padding: 10px; border-radius: 6px; margin-bottom: 10px; border-left: 3px solid #ff6b6b;">
                        <strong style="color: #333;">Notas:</strong> {{ $order->notes }}
                    </div>
                @endif

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.85rem; color: #999;">
                    <div><strong>Creada:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</div>
                    @if ($order->completed_at)
                        <div><strong>Completada:</strong> {{ $order->completed_at->format('d/m/Y H:i') }}</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

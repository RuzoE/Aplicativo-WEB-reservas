# EJEMPLOS DE CÓDIGO - SISTEMA HOTELERO

Documento complementario con ejemplos prácticos de uso y patrones implementados en el sistema.

---

## 1. EJEMPLOS DE MODELOS ELOQUENT

### 1.1 Relaciones Complejas

```php
// app/Models/Stay.php
class Stay extends Model {
    
    // Relaciones básicas
    public function guest() {
        return $this->belongsTo(Guest::class);
    }
    
    public function room() {
        return $this->belongsTo(Room::class);
    }
    
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    // Relación hacia Order (reserva padre)
    public function order() {
        return $this->belongsTo(Order::class, 'reservation_id');
    }
    
    // Folios asociados
    public function folios() {
        return $this->hasMany(Folio::class);
    }
    
    // Accessor: obtener número de habitación desde notas
    public function getAssignedRoomNumberAttribute() {
        if ($this->notes && preg_match('/\[ROOM_NUM:(\d+)\]/', $this->notes, $matches)) {
            return (int) $matches[1];
        }
        return $this->room?->total_room;
    }
    
    // Calcular duración de estancia
    public function getLengthOfStayAttribute() {
        if (!$this->arrival_at || !$this->departure_at) {
            return 0;
        }
        return $this->departure_at->diffInDays($this->arrival_at);
    }
    
    // Scopes para queries comunes
    public function scopeActive($query) {
        return $query->where('status', 'activa');
    }
    
    public function scopeCheckedIn($query) {
        return $query->whereNotNull('actual_check_in_at');
    }
    
    public function scopeCurrentGuests($query) {
        return $query->whereNull('actual_check_out_at')
            ->whereNotNull('actual_check_in_at');
    }
    
    public function scopeByDateRange($query, Carbon $start, Carbon $end) {
        return $query->whereBetween('arrival_at', [$start, $end]);
    }
}
```

### 1.2 Método Complejo: Billing Breakdown

```php
// app/Models/Stay.php
public function getBillingBreakdown(): array {
    $order = $this->order;
    $folios = $this->folios;
    
    // 1. Base de reserva
    $reservationTotal = $order 
        ? $order->total_amount 
        : ($this->daily_rate * max(1, $this->length_of_stay));
    
    // 2. Anticipo pagado (30%)
    $downPayment = $order?->down_payment_amount ?? 0;
    
    // 3. Cargos adicionales
    $additionalCharges = 0;
    $chargesDetail = [];
    
    foreach ($folios as $folio) {
        $folioCharges = $folio->charges;
        foreach ($folioCharges as $charge) {
            // Validar si ya fue pagado
            $isPaid = $folio->payments->contains(function($p) use ($charge) {
                return $p->external_ref === 'Compra:' . $charge->reference_id;
            });
            
            if (!$isPaid) {
                $additionalCharges += $charge->amount;
                $chargesDetail[] = [
                    'description' => $charge->description,
                    'amount' => $charge->amount,
                    'source' => $charge->source,
                ];
            }
        }
    }
    
    // 4. Pagos aplicados
    $totalPayments = 0;
    $paymentsDetail = [];
    
    foreach ($folios as $folio) {
        foreach ($folio->payments as $payment) {
            $totalPayments += $payment->amount;
            $paymentsDetail[] = [
                'method' => $payment->method,
                'amount' => $payment->amount,
                'date' => $payment->received_at,
            ];
        }
    }
    
    // 5. Resumen
    $grandTotal = $reservationTotal + $additionalCharges;
    $remainingBalance = $grandTotal - $totalPayments;
    
    return [
        'reservation_base' => $reservationTotal,
        'down_payment' => $downPayment,
        'additional_charges' => $additionalCharges,
        'charges_detail' => $chargesDetail,
        'total_charges' => $additionalCharges,
        'payments_made' => $totalPayments,
        'payments_detail' => $paymentsDetail,
        'grand_total' => $grandTotal,
        'balance_pending' => max(0, $remainingBalance),
        'num_nights' => $this->length_of_stay,
    ];
}
```

---

## 2. EJEMPLOS DE CONTROLLERS

### 2.1 CheckInController - Búsqueda y Validaciones

```php
// app/Http/Controllers/Reception/CheckInController.php

class CheckInController extends Controller {
    
    public function __construct(protected CheckInService $checkInService) {}
    
    /**
     * Buscar reservas pendientes de check-in
     */
    public function search(Request $request): JsonResponse {
        // Reservas sin Stay activo y con fecha de check-in ≤ hoy
        $reservations = Order::whereDoesntHave('stays')
            ->whereDate('check_in', '<=', now())
            ->with(['user', 'room.roomtype'])
            ->orderBy('check_in', 'asc')
            ->get();
        
        if ($reservations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No hay reservas pendientes de check-in',
            ]);
        }
        
        return response()->json([
            'success' => true,
            'reservations' => $reservations->map(function ($order) {
                return [
                    'id' => $order->id,
                    'codigo' => $this->generateReservationCode($order),
                    'guest_name' => $order->user->name,
                    'guest_email' => $order->user->email,
                    'room_type' => $order->room->roomtype->name ?? 'N/A',
                    'check_in' => $order->check_in->format('Y-m-d H:i'),
                    'check_out' => $order->check_out->format('Y-m-d H:i'),
                    'total_nights' => $order->stayDays,
                    'total_amount' => $order->total_amount,
                ];
            }),
            'count' => $reservations->count(),
        ]);
    }
    
    /**
     * Mostrar formulario de check-in
     */
    public function show($reservationId) {
        $reservation = Order::with('room.roomtype')->findOrFail($reservationId);
        $this->authorize('create', Stay::class);
        
        $reservedRoomTypeId = $reservation->room_type_id;
        $roomNumberOptions = $this->buildRoomNumberOptions(
            $reservedRoomTypeId, 
            $reservationId
        );
        
        return view('reception.check_in', [
            'reservation' => $reservation,
            'roomNumberOptions' => $roomNumberOptions,
            'documentTypes' => ['CC', 'CE', 'PA', 'NIT', 'TI'],
            'countries' => $this->getCountriesList(),
        ]);
    }
    
    /**
     * Procesar check-in
     */
    public function store(Request $request, $reservationId) {
        $this->authorize('create', Stay::class);
        
        $reservation = Order::with('room')->findOrFail($reservationId);
        
        // Validar datos
        $data = $request->validate([
            'room_number' => 'required|integer|min:1',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'document_type' => 'required|string|in:CC,CE,PA,NIT,TI',
            'document_number' => 'required|string|unique:guests,document_number',
            'email' => ['required', 'email', new AllowedEmailDomain()],
            'phone' => ['required', new PhoneNumberByPrefix()],
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
        ]);
        
        // Validar habitación seleccionada
        $reservedRoomTypeId = $reservation->room?->room_type_id;
        $roomNumberOptions = $this->buildRoomNumberOptions(
            $reservedRoomTypeId, 
            $reservationId
        );
        
        $selectedOption = $roomNumberOptions
            ->firstWhere('number', (int)$data['room_number']);
        
        if (!$selectedOption) {
            return back()->withErrors([
                'room_number' => 'Habitación inválida',
            ])->withInput();
        }
        
        if ($selectedOption['status'] !== 'Disponible') {
            return back()->withErrors([
                'room_number' => "No disponible: {$selectedOption['status']}",
            ])->withInput();
        }
        
        // Procesar check-in
        try {
            $stay = $this->checkInService->processCheckIn($reservation, $data);
            
            // Event listener: StayStarted
            event(new StayStarted($stay));
            
            // Registrar en auditoría
            registrarAuditoria(
                'CHECK_IN',
                'recepcion',
                $stay->id,
                "Check-in de {$data['first_name']} en habitación {$data['room_number']}",
                Auth::id(),
                ['skip_duplicate' => true]
            );
            
            return redirect()->route('reception.dashboard')
                ->with('success', 'Check-in registrado exitosamente');
                
        } catch (Exception $e) {
            Log::error('Check-in error', [
                'reservation_id' => $reservationId,
                'error' => $e->getMessage(),
            ]);
            
            return back()->withErrors([
                'general' => 'Error al procesar check-in: ' . $e->getMessage(),
            ])->withInput();
        }
    }
    
    /**
     * Construir opciones de números de habitación disponibles
     */
    private function buildRoomNumberOptions($roomTypeId, $reservationId): Collection {
        // Habitaciones activas del tipo
        $activeRooms = Room::where('room_type_id', $roomTypeId)
            ->where('status', 'activa')
            ->get();
        
        if ($activeRooms->isEmpty()) {
            return collect([]);
        }
        
        // Habitaciones ya asignadas (ocupadas)
        $assignedRoomIds = Stay::whereNull('actual_check_out_at')
            ->whereNotNull('actual_check_in_at')
            ->pluck('room_id')
            ->toArray();
        
        $options = [];
        foreach ($activeRooms as $room) {
            $isOccupied = in_array($room->id, $assignedRoomIds);
            
            $options[] = [
                'id' => $room->id,
                'number' => $room->total_room,
                'capacity' => $room->total_room,
                'status' => $isOccupied ? 'Ocupada' : 'Disponible',
                'floor' => $this->getFloorFromRoomNumber($room->total_room),
            ];
        }
        
        return collect($options)->sortBy('number');
    }
    
    private function generateReservationCode(Order $order): string {
        return 'RES-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
    }
    
    private function getFloorFromRoomNumber(int $roomNumber): int {
        // Ej: 301 → piso 3
        return intval(substr($roomNumber, 0, -2)) ?: 1;
    }
    
    private function getCountriesList(): array {
        return [
            'CO' => 'Colombia',
            'US' => 'Estados Unidos',
            'MX' => 'México',
            'AR' => 'Argentina',
            'BR' => 'Brasil',
            // ... más países
        ];
    }
}
```

### 2.2 MinibarCheckoutController

```php
// app/Http/Controllers/Minibar/User/CheckoutController.php

class CheckoutController extends Controller {
    
    public function __construct(private AuditoriaService $auditoria) {}
    
    /**
     * Procesar checkout de minibar
     * 
     * @param Request $request Contiene items del carrito
     * @return JsonResponse
     * @throws ValidationException
     */
    public function checkout(Request $request): JsonResponse {
        $user = Auth::user();
        
        // Obtener stay actual del usuario
        $stay = Stay::where('user_id', $user->id)
            ->whereNull('actual_check_out_at')
            ->whereNotNull('actual_check_in_at')
            ->first();
        
        if (!$stay) {
            return response()->json([
                'success' => false,
                'message' => 'No hay estancia activa',
            ], 422);
        }
        
        // Validar items
        $items = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
        
        // Verificar stock
        $cart = $items['items'];
        $compraItems = [];
        $totalAmount = 0;
        
        foreach ($cart as $item) {
            $product = MinibarProduct::find($item['product_id']);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => "Producto {$item['product_id']} no existe",
                ], 404);
            }
            
            if ($product->stock < $item['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock insuficiente de {$product->nombre}",
                ], 422);
            }
            
            $amount = $product->precio * $item['quantity'];
            $totalAmount += $amount;
            
            $compraItems[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'precio_unitario' => $product->precio,
                'amount' => $amount,
            ];
        }
        
        // Crear compra
        $compra = Compra::create([
            'user_id' => $user->id,
            'stay_id' => $stay->id,
            'posted_at' => now(),
            'posted_by' => $user->id,
        ]);
        
        // Registrar items
        foreach ($compraItems as $item) {
            $compra->productos()->attach($item['product_id'], [
                'cantidad' => $item['quantity'],
                'precio_unitario' => $item['precio_unitario'],
            ]);
            
            // Reducir stock
            $product = MinibarProduct::find($item['product_id']);
            $product->decrement('stock', $item['quantity']);
        }
        
        // Agregar charge al folio
        $folio = $stay->folios()->firstOrCreate([
            'stay_id' => $stay->id,
        ], [
            'number' => $this->generateFolioNumber($stay),
            'status' => 'open',
            'balance' => 0,
        ]);
        
        $charge = $folio->charges()->create([
            'source' => 'Minibar',
            'description' => "Compra minibar #{$compra->id}",
            'amount' => $totalAmount,
            'tax' => 0,
            'posted_at' => now(),
            'posted_by' => $user->id,
            'reference_type' => 'Compra',
            'reference_id' => $compra->id,
        ]);
        
        // Actualizar balance del folio
        $folio->balance += $charge->amount;
        $folio->save();
        
        // Auditoría
        $this->auditoria->registrar(
            'MINIBAR_CHECKOUT',
            'minibar',
            $compra->id,
            "Compra minibar por " . $user->name . " Cantidad: " . count($cart),
            $user->id
        );
        
        return response()->json([
            'success' => true,
            'compra_id' => $compra->id,
            'folio_id' => $folio->id,
            'total_amount' => $totalAmount,
            'balance' => $folio->balance,
            'message' => 'Compra registrada exitosamente',
        ]);
    }
    
    private function generateFolioNumber(Stay $stay): string {
        $folioCount = $stay->folios()->count() + 1;
        return "Folio-{$stay->id}-" . str_pad($folioCount, 3, '0', STR_PAD_LEFT);
    }
}
```

---

## 3. EJEMPLOS DE FORM REQUESTS

### 3.1 Validación Check-in

```php
// app/Http/Requests/StoreCheckInRequest.php

class StoreCheckInRequest extends FormRequest {
    
    public function authorize(): bool {
        // Solo receptistas pueden registrar check-in
        return $this->user()->hasRole('recepcion');
    }
    
    public function rules(): array {
        return [
            'room_number' => [
                'required',
                'integer',
                'min:1',
                new RoomNumberValid($this->input('reservation_id')),
            ],
            'first_name' => 'required|string|max:100|regex:/^[a-záéíóúñ\s]+$/i',
            'last_name' => 'required|string|max:100|regex:/^[a-záéíóúñ\s]+$/i',
            'document_type' => 'required|string|in:CC,CE,PA,NIT,TI',
            'document_number' => [
                'required',
                'string',
                'max:100',
                'unique:guests,document_number',
                new ValidDocumentNumber($this->input('document_type')),
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:100',
                new AllowedEmailDomain(),
            ],
            'phone' => [
                'required',
                new PhoneNumberByPrefix(),
            ],
            'country' => 'nullable|string|max:100',
            'adults' => 'required|integer|min:1|max:10',
            'children' => 'nullable|integer|min:0|max:10',
            'notes' => 'nullable|string|max:500',
        ];
    }
    
    public function messages(): array {
        return [
            'room_number.required' => 'Debe seleccionar una habitación',
            'room_number.integer' => 'El número de habitación debe ser numérico',
            'document_number.unique' => 'Este documento ya existe en el sistema',
            'email.email' => 'Email inválido',
            'first_name.regex' => 'El nombre contiene caracteres no permitidos',
            'phone.required' => 'El teléfono es obligatorio',
        ];
    }
    
    public function validated(): array {
        $data = parent::validated();
        
        // Normalizar nombres
        $data['first_name'] = trim(ucwords(strtolower($data['first_name'])));
        $data['last_name'] = trim(ucwords(strtolower($data['last_name'])));
        
        // Limpiar teléfono
        $data['phone'] = preg_replace('/[^\d+]/', '', $data['phone']);
        
        return $data;
    }
}
```

---

## 4. EJEMPLOS DE SERVICIOS

### 4.1 CheckInService

```php
// app/Services/Reception/CheckInService.php

class CheckInService {
    
    public function __construct(private AuditoriaService $auditoria) {}
    
    /**
     * Procesar check-in completo
     */
    public function processCheckIn(Order $order, array $data): Stay {
        // 1. Crear guest
        $guest = Guest::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'document_type' => $data['document_type'],
            'document_number' => $data['document_number'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'country' => $data['country'] ?? null,
        ]);
        
        // 2. Crear stay
        $stay = Stay::create([
            'reservation_id' => $order->id,
            'guest_id' => $guest->id,
            'room_id' => $order->room_id,
            'user_id' => Auth::id(),
            'status' => 'activa',
            'arrival_at' => $order->check_in,
            'departure_at' => $order->check_out,
            'actual_check_in_at' => now(),
            'adults' => $data['adults'] ?? 1,
            'children' => $data['children'] ?? 0,
            'rate_plan' => 'basic',
            'daily_rate' => $order->room?->price ?? 0,
            'notes' => "[ROOM_NUM:{$data['room_number']}]",
        ]);
        
        // 3. Crear folio
        $folio = Folio::create([
            'stay_id' => $stay->id,
            'number' => $this->generateFolioNumber($stay),
            'status' => 'open',
            'currency' => 'COP',
            'balance' => 0, // Se actualiza con cargos
        ]);
        
        // 4. Agregar cargo base (reserva)
        $charge = $folio->charges()->create([
            'source' => 'Reservation',
            'description' => 'Cargo base de reserva',
            'amount' => $order->total_amount,
            'posted_at' => now(),
            'posted_by' => Auth::id(),
        ]);
        
        $folio->increment('balance', $charge->amount);
        
        // 5. Actualizar status de order
        $order->update(['status' => Order::STATUS_OCUPADA]);
        
        // 6. Registrar auditoría
        $this->auditoria->registrar(
            'CHECK_IN',
            'recepcion',
            $stay->id,
            "Check-in de {$guest->first_name} {$guest->last_name}",
            Auth::id()
        );
        
        return $stay;
    }
    
    /**
     * Generar número de folio único
     */
    private function generateFolioNumber(Stay $stay): string {
        $prefix = 'FOLIO-' . $stay->id . '-';
        $nextNumber = $stay->folios()->count() + 1;
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
```

---

## 5. EJEMPLOS DE QUERIES Y SCOPES

### 5.1 Queries de Negocio

```php
// Obtener disponibilidad de habitaciones
$availableRooms = Room::where('status', 'activa')
    ->whereDoesntHave('stays', function ($q) {
        $q->whereNotNull('actual_check_in_at')
          ->whereNull('actual_check_out_at')
          ->whereBetween('arrival_at', [now(), $checkoutDate]);
    })
    ->with('roomtype')
    ->get();

// Huéspedes actualmente en el hotel
$currentGuests = Stay::with(['guest', 'room.roomtype'])
    ->whereNull('actual_check_out_at')
    ->whereNotNull('actual_check_in_at')
    ->orderByDesc('actual_check_in_at')
    ->get();

// Reservas siguientes (próximas 7 días)
$upcomingReservations = Order::whereDoesntHave('stays')
    ->whereBetween('check_in', [now(), now()->addDays(7)])
    ->with('user', 'room.roomtype')
    ->orderBy('check_in')
    ->get();

// Órdenes de mantenimiento no completadas por tipo
$maintenanceByType = MaintenanceOrder::active()
    ->select('priority', DB::raw('count(*) as count'))
    ->groupBy('priority')
    ->get();

// Ingresos diarios
$dailyRevenue = Order::with('stays')
    ->whereDate('created_at', today())
    ->get()
    ->sum('total_amount');

// Ventas minibar últimos 30 días
$minibarSales = Compra::whereBetween('created_at', [
    now()->subDays(30),
    now()
])
    ->with('productos')
    ->get()
    ->map(fn($c) => [
        'id' => $c->id,
        'total' => $c->productos->sum(
            fn($p) => $p->pivot->cantidad * $p->pivot->precio_unitario
        ),
        'items' => $c->productos()->count(),
        'date' => $c->created_at,
    ]);
```

### 5.2 Scopes Reutilizables

```php
// app/Models/Order.php
class Order extends Model {
    // Reservas activas (no completadas ni canceladas)
    public function scopeActive($query) {
        return $query->whereIn('status', [
            self::STATUS_ANTICIPO_PAGADO,
            self::STATUS_RESERVA_PREVIA,
            self::STATUS_OCUPADA,
        ]);
    }
    
    // Reservas con pago pendiente
    public function scopePendingPayment($query) {
        return $query->where('status', self::STATUS_PENDIENTE)
            ->orWhere(function ($q) {
                $q->where('is_paid', false)
                  ->where('status', '!=', self::STATUS_FINALIZADA);
            });
    }
    
    // Filtrar por rango de fechas
    public function scopeByDateRange($query, $start, $end) {
        return $query->whereBetween('check_in', [$start, $end]);
    }
}

// Uso
$orders = Order::active()
    ->pendingPayment()
    ->byDateRange($startDate, $endDate)
    ->with('user')
    ->get();
```

---

## 6. EJEMPLOS DE VALIDACIÓN CUSTOM

### 6.1 Reglas Personalizadas

```php
// app/Rules/AllowedEmailDomain.php
namespace App\Rules;

class AllowedEmailDomain implements ValidationRule {
    
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $allowedDomains = config('app.allowed_email_domains', [
            'gmail.com',
            'hotmail.com',
            'empresa.com',
        ]);
        
        $domain = substr(strrchr($value, "@"), 1);
        
        if (!in_array($domain, $allowedDomains)) {
            $fail(":attribute debe ser de un dominio permitido.");
        }
    }
}

// app/Rules/PhoneNumberByPrefix.php
class PhoneNumberByPrefix implements ValidationRule {
    
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        // Validar formato E.164
        $pattern = '/^\+?[1-9]\d{1,14}$/';
        
        if (!preg_match($pattern, $value)) {
            $fail(":attribute debe ser un número válido (ej: +573001234567)");
        }
    }
}

// app/Rules/RoomNumberValid.php
class RoomNumberValid implements ValidationRule {
    
    public function __construct(private string $reservationId) {}
    
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        $reservation = Order::find($this->reservationId);
        
        if (!$reservation) {
            $fail("Reserva inválida");
            return;
        }
        
        // Validar que la habitación existe y está disponible
        $room = Room::where('total_room', $value)
            ->where('room_type_id', $reservation->room_type_id)
            ->where('status', 'activa')
            ->first();
        
        if (!$room) {
            $fail(":attribute no existe o no está disponible");
        }
        
        // Validar que no esté ocupada
        if ($room->stays()
            ->whereNull('actual_check_out_at')
            ->whereNotNull('actual_check_in_at')
            ->exists()) {
            $fail(":attribute ya está ocupada");
        }
    }
}
```

---

## 7. EJEMPLOS DE EVENTOS Y LISTENERS

### 7.1 Event: StayStarted

```php
// app/Events/Reception/StayStarted.php
class StayStarted {
    public function __construct(public Stay $stay) {}
    
    public function broadcastOn(): array {
        return [
            new PrivateChannel('reception'),
        ];
    }
}

// app/Listeners/SendWelcomeEmail.php
class SendWelcomeEmail {
    
    public function __construct(private Mail $mail) {}
    
    public function handle(StayStarted $event) {
        $stay = $event->stay;
        $guest = $stay->guest;
        $room = $stay->room;
        
        // Enviar email de bienvenida
        $this->mail->send(new WelcomeMail($guest, $room));
    }
}

// app/Listeners/CreateFolioRecord.php
class CreateFolioRecord {
    
    public function handle(StayStarted $event) {
        $stay = $event->stay;
        
        // El folio ya debería estar creado en CheckInService
        // Este listener agrega datos adicionales si es necesario
    }
}

// config/event.php - Registrar
'events' => [
    StayStarted::class => [
        SendWelcomeEmail::class,
        CreateFolioRecord::class,
    ],
]
```

---

## 8. EJEMPLOS DE API REST

### 8.1 Request y Response JSON

```
// GET /api/minibar-products
// Response 200
{
    "data": [
        {
            "id": 1,
            "nombre": "Corona Extra",
            "descripcion": "Cerveza clara",
            "precio": 15000,
            "stock": 50,
            "tipo": {
                "id": 1,
                "nombre": "Cerveza",
                "es_alcoholica": true
            },
            "created_at": "2026-03-15T10:30:00Z"
        },
        ...
    ],
    "pagination": {
        "total": 45,
        "per_page": 15,
        "current_page": 1,
        "last_page": 3,
        "from": 1,
        "to": 15
    }
}

// POST /api/minibar-products
// Request
{
    "bebida_type_id": 1,
    "nombre": "Heineken",
    "descripcion": "Cerveza holandesa",
    "precio": 16500,
    "stock": 45,
    "imagen": "heineken.jpg"
}

// Response 201
{
    "id": 2,
    "nombre": "Heineken",
    "precio": 16500,
    "stock": 45,
    "mensaje": "Producto creado exitosamente"
}

// PUT /api/minibar-products/2
// Request
{
    "stock": 60
}

// Response 200
{
    "id": 2,
    "nombre": "Heineken",
    "stock": 60,
    "mensaje": "Producto actualizado"
}

// DELETE /api/minibar-products/2
// Response 204 No Content
```

---

## 9. EJEMPLOS DE TESTING

### 9.1 Feature Test: Check-in

```php
// tests/Feature/Reception/CheckInTest.php

class CheckInTest extends TestCase {
    use RefreshDatabase;
    
    private User $adminUser;
    private Order $reservation;
    
    protected function setUp(): void {
        parent::setUp();
        
        $this->adminUser = User::factory()
            ->create(['is_admin' => true]);
        
        $room = Room::factory()->create();
        
        $this->reservation = Order::factory()
            ->create([
                'room_id' => $room->id,
                'check_in' => now()->subDay(),
                'check_out' => now()->addDays(2),
            ]);
    }
    
    /** @test */
    public function can_search_pending_check_ins() {
        // Verificar que reserva aparece en búsqueda
        $response = $this->actingAs($this->adminUser)
            ->post('/reception/check-in/search');
        
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('reservations.0.id', $this->reservation->id);
    }
    
    /** @test */
    public function can_complete_check_in_with_valid_data() {
        $checkInData = [
            'room_number' => 101,
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'document_type' => 'CC',
            'document_number' => '1234567890',
            'email' => 'juan@example.com',
            'phone' => '+573001234567',
            'adults' => 2,
            'children' => 0,
        ];
        
        $response = $this->actingAs($this->adminUser)
            ->post("/reception/check-in/{$this->reservation->id}", $checkInData);
        
        $response->assertStatus(302)
            ->assertRedirect(route('reception.dashboard'));
        
        // Validar Stay fue creado
        $this->assertDatabaseHas('stays', [
            'reservation_id' => $this->reservation->id,
            'status' => 'activa',
        ]);
        
        // Validar Guest fue creado
        $this->assertDatabaseHas('guests', [
            'document_number' => '1234567890',
            'first_name' => 'Juan',
        ]);
        
        // Validar Auditoria fue registrada
        $this->assertDatabaseHas('auditorias', [
            'usuario_id' => $this->adminUser->id,
            'accion' => 'CHECK_IN',
        ]);
    }
    
    /** @test */
    public function fails_check_in_with_duplicate_document() {
        // Crear guest con documento
        Guest::factory()->create(['document_number' => '1234567890']);
        
        $checkInData = [
            'document_number' => '1234567890',
            'room_number' => 101,
            'first_name' => 'Juan',
            // ... resto de datos
        ];
        
        $response = $this->actingAs($this->adminUser)
            ->post("/reception/check-in/{$this->reservation->id}", $checkInData);
        
        $response->assertStatus(302)
            ->assertSessionHasErrors('document_number');
    }
    
    /** @test */
    public function only_authorized_users_can_check_in() {
        $regularUser = User::factory()->create(['is_admin' => false]);
        
        $response = $this->actingAs($regularUser)
            ->post("/reception/check-in/{$this->reservation->id}", []);
        
        $response->assertStatus(403);
    }
}
```

---

**Fin de ejemplos de código**

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    rooms: {
        type: Array,
        required: true
    },
    selectedOrder: {
        type: Object,
        default: null
    },
    assignUrl: {
        type: String,
        required: true
    },
    csrfToken: {
        type: String,
        required: true
    },
    roomsByDateUrl: {
        type: String,
        required: true
    }
});

const searchQuery = ref('');
const showConfirmModal = ref(false);
const roomToAssign = ref(null);

const selectedDate = ref(new Date().toISOString().split('T')[0]);
const roomsData = ref(props.rooms);
const isLoading = ref(false);

const showInfoModal = ref(false);
const selectedRoomInfo = ref(null);

const fetchRoomsByDate = async () => {
    if (!props.roomsByDateUrl || !selectedDate.value) return;
    
    isLoading.value = true;
    try {
        const url = new URL(props.roomsByDateUrl, window.location.origin);
        url.searchParams.append('date', selectedDate.value);
        
        const response = await fetch(url.toString(), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) throw new Error('Error network');
        const data = await response.json();
        roomsData.value = data.rooms;
    } catch (error) {
        console.error('Error fetching rooms by date:', error);
    } finally {
        isLoading.value = false;
    }
};

watch(selectedDate, () => {
    fetchRoomsByDate();
});

const filteredRooms = computed(() => {
    if (!searchQuery.value) return roomsData.value;
    return roomsData.value.filter(room => 
        room.number.includes(searchQuery.value) || 
        room.type_name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const selectRoom = (room) => {
    if (props.selectedOrder) {
        if (room.status !== 'disponible') return;
        roomToAssign.value = room;
        showConfirmModal.value = true;
    } else {
        // Modo consulta de información
        if (room.status === 'disponible' || room.status === 'pre-reserva' || room.status === 'ocupada') {
            selectedRoomInfo.value = room;
            showInfoModal.value = true;
        }
    }
};

const confirmAssignment = () => {
    if (roomToAssign.value) {
        assignRoom(roomToAssign.value);
    }
};

const cancelAssignment = () => {
    showConfirmModal.value = false;
    roomToAssign.value = null;
};

const closeInfoModal = () => {
    showInfoModal.value = false;
    selectedRoomInfo.value = null;
};

const assignRoom = (room) => {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = props.assignUrl.replace('__ROOM_ID__', room.id);
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = props.csrfToken;
    
    const roomNumInput = document.createElement('input');
    roomNumInput.type = 'hidden';
    roomNumInput.name = 'room_number';
    roomNumInput.value = room.number;
    
    form.appendChild(csrfInput);
    form.appendChild(roomNumInput);
    document.body.appendChild(form);
    form.submit();
};
</script>

<template>
    <div class="space-y-8">
        <!-- Dashboard Header & Legend -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex-1 flex flex-col md:flex-row md:items-center gap-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Estado de Habitaciones</h3>
                    <p class="text-sm text-gray-400">Seleccione una habitación libre para asignar</p>
                </div>
                
                <!-- Date Picker -->
                <div class="relative flex items-center md:ml-6">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-calendar-event text-gray-400"></i>
                    </div>
                    <input 
                        type="date" 
                        v-model="selectedDate"
                        class="pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 bg-gray-50 shadow-sm font-medium transition-colors cursor-pointer"
                        :disabled="isLoading"
                    />
                    <div v-if="isLoading" class="absolute right-[-30px] w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
            </div>
            
            <div class="flex flex-wrap justify-center gap-6 mt-4 md:mt-0">
                <div class="flex items-center gap-3">
                    <div class="w-4 h-4 bg-green-500 rounded-lg shadow-sm"></div>
                    <span class="text-sm font-bold text-gray-600">Disponible</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-4 h-4 bg-red-500 rounded-lg shadow-sm"></div>
                    <span class="text-sm font-bold text-gray-600">Ocupada</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-4 h-4 bg-yellow-500 rounded-lg shadow-sm"></div>
                    <span class="text-sm font-bold text-gray-600">Mantenimiento</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-4 h-4 bg-blue-500 rounded-lg shadow-sm"></div>
                    <span class="text-sm font-bold text-gray-600">Pre-Reserva</span>
                </div>
            </div>
        </div>

        <!-- Room Grid (4-5 per row) -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6" :class="{ 'opacity-50 pointer-events-none': isLoading }">
            <div 
                v-for="room in filteredRooms" 
                :key="room.number"
                @click="selectRoom(room)"
                :class="[
                    'group relative rounded-xl shadow p-6 text-center transition-all duration-300 transform',
                    room.status === 'disponible' ? 'bg-green-50 border-2 border-green-200 cursor-pointer hover:scale-105 hover:shadow-xl hover:border-green-400' : '',
                    room.status === 'ocupada' ? 'bg-red-50 border-2 border-red-200 opacity-80 cursor-pointer hover:scale-105 hover:border-red-400' : '',
                    room.status === 'mantenimiento' ? 'bg-yellow-50 border-2 border-yellow-200 opacity-80 cursor-not-allowed' : '',
                    room.status === 'pre-reserva' ? 'bg-blue-50 border-2 border-blue-200 opacity-90 cursor-pointer hover:scale-105 hover:shadow-xl hover:border-blue-400' : '',
                    selectedOrder && room.status === 'disponible' ? 'ring-4 ring-blue-400 ring-opacity-50' : ''
                ]"
            >
                <!-- Room Number (Large) -->
                <div 
                    :class="[
                        'text-5xl font-black mb-2 transition-colors',
                        room.status === 'disponible' ? 'text-green-600' : '',
                        room.status === 'ocupada' ? 'text-red-600' : '',
                        room.status === 'mantenimiento' ? 'text-yellow-600' : '',
                        room.status === 'pre-reserva' ? 'text-blue-600' : ''
                    ]"
                >
                    {{ room.number }}
                </div>

                <!-- Room Type (Small) -->
                <div 
                    :class="[
                        'text-xs font-bold uppercase tracking-widest opacity-75',
                        room.status === 'disponible' ? 'text-green-800' : '',
                        room.status === 'ocupada' ? 'text-red-800' : '',
                        room.status === 'mantenimiento' ? 'text-yellow-800' : '',
                        room.status === 'pre-reserva' ? 'text-blue-800' : ''
                    ]"
                >
                    {{ room.type_name }}
                </div>

                <!-- Status Overlay Icon -->
                <div class="absolute top-3 right-3 opacity-20 group-hover:opacity-40 transition-opacity">
                    <i v-if="room.status === 'disponible'" class="bi bi-check-circle-fill text-xl"></i>
                    <i v-if="room.status === 'ocupada'" class="bi bi-person-fill text-xl text-red-700"></i>
                    <i v-if="room.status === 'mantenimiento'" class="bi bi-tools text-xl"></i>
                    <i v-if="room.status === 'pre-reserva'" class="bi bi-bookmark-fill text-xl text-blue-700"></i>
                </div>
                
                <!-- Guest Name if pre-reserva or ocupada -->
                <div v-if="(room.status === 'pre-reserva' || room.status === 'ocupada') && room.guest_name" class="mt-3 text-sm font-semibold truncate px-2" :class="room.status === 'pre-reserva' ? 'text-blue-800' : 'text-red-800'">
                    <i class="bi bi-person me-1"></i>{{ room.guest_name }}
                </div>

                <!-- Interaction Hint -->
                <div v-if="room.status === 'disponible' && selectedOrder" class="mt-4 text-[10px] font-black text-blue-600 animate-bounce uppercase">
                    ¡Click para asignar!
                </div>
            </div>
        </div>
        
        <!-- Empty State -->
        <div v-if="filteredRooms.length === 0 && !isLoading" class="text-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
            <i class="bi bi-inbox text-5xl text-gray-300 mb-4 block"></i>
            <p class="text-gray-500 font-medium text-xl">No se encontraron habitaciones</p>
            <p class="text-gray-400">Intente buscar otro número o tipo de habitación.</p>
        </div>

        <!-- Info Modal -->
        <div v-if="showInfoModal && selectedRoomInfo" class="fixed inset-0 z-50 flex items-center justify-center transition-opacity" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl transform transition-transform scale-100">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-2xl font-black text-gray-800">Habitación {{ selectedRoomInfo.number }}</h3>
                    <button @click="closeInfoModal" class="text-gray-400 hover:text-gray-600">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>
                
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-3 py-1 text-sm font-bold rounded-full" 
                            :class="[
                                selectedRoomInfo.status === 'disponible' ? 'bg-green-100 text-green-700' : '',
                                selectedRoomInfo.status === 'pre-reserva' ? 'bg-blue-100 text-blue-700' : '',
                                selectedRoomInfo.status === 'ocupada' ? 'bg-red-100 text-red-700' : ''
                            ]">
                            {{ selectedRoomInfo.status === 'pre-reserva' ? 'Pre-Reserva' : selectedRoomInfo.status.charAt(0).toUpperCase() + selectedRoomInfo.status.slice(1) }}
                        </span>
                        <span class="text-sm font-bold text-gray-500 uppercase">{{ selectedRoomInfo.type_name }}</span>
                    </div>

                    <div v-if="selectedRoomInfo.status === 'disponible'" class="text-gray-600 bg-green-50 p-4 rounded-xl border border-green-100">
                        <p class="font-medium"><i class="bi bi-check-circle text-green-500 me-2"></i> Esta habitación está disponible para asignar el <strong>{{ selectedDate }}</strong>.</p>
                    </div>

                    <div v-if="selectedRoomInfo.status === 'pre-reserva' && selectedRoomInfo.order_info" class="space-y-3 bg-blue-50 p-4 rounded-xl border border-blue-100">
                        <p class="text-sm text-blue-800 font-semibold mb-2"><i class="bi bi-journal-bookmark-fill me-2"></i>Detalles de Reserva</p>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="col-span-2">
                                <span class="text-blue-500 text-xs font-bold uppercase block">Huésped</span>
                                <span class="font-semibold text-gray-800">{{ selectedRoomInfo.order_info.guest_name || 'No especificado' }}</span>
                            </div>
                            <div>
                                <span class="text-blue-500 text-xs font-bold uppercase block">Llegada</span>
                                <span class="font-semibold text-gray-800">{{ selectedRoomInfo.order_info.check_in }}</span>
                            </div>
                            <div>
                                <span class="text-blue-500 text-xs font-bold uppercase block">Salida</span>
                                <span class="font-semibold text-gray-800">{{ selectedRoomInfo.order_info.check_out }}</span>
                            </div>
                        </div>
                    </div>

                    <div v-if="selectedRoomInfo.status === 'ocupada' && selectedRoomInfo.order_info" class="space-y-3 bg-red-50 p-4 rounded-xl border border-red-100">
                        <p class="text-sm text-red-800 font-semibold mb-2"><i class="bi bi-house-door-fill me-2"></i>Estancia Actual</p>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="col-span-2">
                                <span class="text-red-500 text-xs font-bold uppercase block">Huésped en Casa</span>
                                <span class="font-semibold text-gray-800">{{ selectedRoomInfo.order_info.guest_name || 'No especificado' }}</span>
                            </div>
                            <div>
                                <span class="text-red-500 text-xs font-bold uppercase block">Check-in</span>
                                <span class="font-semibold text-gray-800">{{ selectedRoomInfo.order_info.check_in }}</span>
                            </div>
                            <div>
                                <span class="text-red-500 text-xs font-bold uppercase block">Check-out (previsto)</span>
                                <span class="font-semibold text-gray-800">{{ selectedRoomInfo.order_info.check_out }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button @click="closeInfoModal" class="w-full py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

        <!-- Custom Confirmation Modal (Asignación) -->
        <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center transition-opacity" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl transform transition-transform scale-100">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-question-lg text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 mb-2">Confirmar Asignación</h3>
                    <p class="text-gray-600">
                        ¿Estás seguro de asignar la <strong class="text-gray-900">Habitación {{ roomToAssign?.number }} ({{ roomToAssign?.type_name }})</strong> a <strong class="text-blue-600">{{ selectedOrder?.nombre_cliente || selectedOrder?.user?.name || 'el huésped' }}</strong>?
                    </p>
                </div>
                <div class="flex gap-4">
                    <button @click="cancelAssignment" class="flex-1 py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors">
                        Cancelar
                    </button>
                    <button @click="confirmAssignment" class="flex-1 py-3 px-4 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-xl shadow-lg transition-colors">
                        Sí, asignar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Transiciones suaves */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>

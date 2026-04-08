<template>
  <div class="reservation-date-modifier p-2">
    <form :action="updateUrl" method="POST" @submit="handleSubmit">
      <input type="hidden" name="_token" :value="csrfToken">
      <input type="hidden" name="_method" value="PUT">

      <div class="row g-4 align-items-center">
        <!-- Tarjeta de Info de Duración -->
        <div class="col-12 mb-2">
          <div class="d-flex align-items-center p-3 bg-light rounded-4 border-start border-primary border-4 shadow-sm">
            <div class="icon-box me-3 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
              <i class="fas fa-history fs-4"></i>
            </div>
            <div>
              <h5 class="mb-0 fw-bold">Duración Original</h5>
              <p class="mb-0 text-muted">{{ duration }} {{ duration == 1 ? 'noche' : 'noches' }} (Se mantiene fija)</p>
            </div>
          </div>
        </div>

        <!-- Nuevo Check-in -->
        <div class="col-md-6">
          <label class="form-label fw-bold h6 text-dark mb-2">
            <i class="fas fa-sign-in-alt text-success me-1"></i> Nueva Fecha de Entrada (Check-in)
          </label>
          <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
            <span class="input-group-text bg-white border-end-0 text-success">
              <i class="far fa-calendar-check"></i>
            </span>
            <input 
              type="date" 
              name="check_in" 
              v-model="newCheckIn" 
              class="form-control border-start-0 ps-0"
              :min="minDate"
              required
            >
          </div>
          <small class="text-muted mt-2 d-block">
            <i class="fas fa-info-circle me-1"></i> Seleccione el día que desea iniciar su estancia.
          </small>
        </div>

        <!-- Nuevo Check-out (Calculado) -->
        <div class="col-md-6">
          <label class="form-label fw-bold h6 text-dark mb-2 opacity-75">
            <i class="fas fa-sign-out-alt text-danger me-1"></i> Nueva Fecha de Salida (Check-out)
          </label>
          <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden opacity-75">
            <span class="input-group-text bg-light border-end-0 text-danger">
              <i class="far fa-calendar-times"></i>
            </span>
            <input 
              type="date" 
              :value="calculatedCheckOut" 
              class="form-control border-start-0 ps-0 bg-light"
              readonly
              disabled
            >
          </div>
          <small class="text-muted mt-2 d-block">
             Este campo se calcula automáticamente para mantener las {{ duration }} noches.
          </small>
        </div>

        <!-- Botones -->
        <div class="col-12 mt-5">
          <div class="d-flex flex-column flex-md-row gap-3">
            <button 
              type="submit" 
              class="btn btn-primary btn-xl flex-grow-1 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center"
              :disabled="loading"
            >
              <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
              <i v-else class="fas fa-save me-2"></i> Actualizar Fecha
            </button>
            <a href="/orders" class="btn btn-outline-secondary btn-xl py-3 px-5 rounded-pill fw-bold">
              Cancelar
            </a>
          </div>
        </div>
      </div>
    </form>

    <!-- Resumen de Cambios -->
    <div class="mt-5 p-4 bg-blue-50 rounded-4 border-1 border-blue-100" v-if="newCheckIn !== initialCheckIn">
      <h6 class="fw-bold text-blue-800 mb-3"><i class="fas fa-sync me-2"></i> Resumen de la nueva programación:</h6>
      <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-blue-200">
        <span class="text-blue-700">Actual:</span>
        <span class="fw-medium">{{ formatDate(initialCheckIn) }} al {{ formatDate(initialCheckOut) }}</span>
      </div>
      <div class="d-flex justify-content-between align-items-center py-2">
        <span class="text-blue-700">Nueva:</span>
        <span class="fw-bold text-blue-900">{{ formatDate(newCheckIn) }} al {{ formatDate(calculatedCheckOut) }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    orderId: { type: [String, Number], required: true },
    initialCheckIn: { type: String, required: true },
    duration: { type: [String, Number], required: true },
    updateUrl: { type: String, required: true },
    csrfToken: { type: String, required: true }
  },
  data() {
    return {
      newCheckIn: this.initialCheckIn,
      loading: false,
      minDate: new Date().toISOString().split('T')[0]
    };
  },
  computed: {
    initialCheckOut() {
      const date = new Date(this.initialCheckIn + 'T00:00:00');
      date.setDate(date.getDate() + parseInt(this.duration));
      return date.toISOString().split('T')[0];
    },
    calculatedCheckOut() {
      if (!this.newCheckIn) return '';
      const date = new Date(this.newCheckIn + 'T00:00:00');
      date.setDate(date.getDate() + parseInt(this.duration));
      return date.toISOString().split('T')[0];
    }
  },
  methods: {
    handleSubmit() {
      this.loading = true;
    },
    formatDate(dateStr) {
      if (!dateStr) return '';
      const date = new Date(dateStr + 'T00:00:00');
      return date.toLocaleDateString('es-ES', { 
        weekday: 'short', 
        day: 'numeric', 
        month: 'short', 
        year: 'numeric' 
      });
    }
  }
};
</script>

<style scoped>
.btn-xl {
  font-size: 1.1rem;
}
.bg-blue-50 {
  background-color: #f0f7ff;
}
.border-blue-100 {
  border-color: #e0efff;
}
.text-blue-800 {
  color: #1e40af;
}
.text-blue-700 {
  color: #1d4ed8;
}
.text-blue-900 {
  color: #1e3a8a;
}
.border-blue-200 {
  border-color: #bfdbfe;
}
</style>

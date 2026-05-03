<script setup>
import { ref } from 'vue';
import { useForm, router, Link } from '@inertiajs/vue3';

const props = defineProps({
  rabs: Array
});

const isModalOpen = ref(false);

const form = useForm({
  project_name: '',
  customer_name: '',
  date: new Date().toLocaleDateString('en-CA'),
  items: [
    { description: '', quantity: 1, unit_price: 0 }
  ]
});

const displayAmount = ref(['0']);

const addItem = () => {
  form.items.push({ description: '', quantity: 1, unit_price: 0 });
  displayAmount.value.push('0');
};

const removeItem = (index) => {
  if (form.items.length > 1) {
    form.items.splice(index, 1);
    displayAmount.value.splice(index, 1);
  }
};

const formatInputAmount = (e, index) => {
  let val = e.target.value.replace(/\D/g, '');
  if (val === '') {
    displayAmount.value[index] = '';
    form.items[index].unit_price = 0;
    return;
  }
  form.items[index].unit_price = parseInt(val, 10);
  displayAmount.value[index] = parseInt(val, 10).toLocaleString('id-ID');
};

const submit = () => {
  form.post('/rab', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      form.items = [{ description: '', quantity: 1, unit_price: 0 }];
      displayAmount.value = ['0'];
      isModalOpen.value = false;
    }
  });
};

const deleteRab = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus RAB ini?')) {
    router.delete(`/rab/${id}`, { preserveScroll: true });
  }
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value);
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('id-ID', options);
};
</script>

<template>
  <div>
    <header class="header">
      <div class="header-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        Rencana Anggaran Biaya (RAB)
      </div>
      <div>
        <Link href="/" class="btn" style="color: white; margin-right: 1rem;">Kembali ke Keuangan</Link>
        <button @click="isModalOpen = true" class="btn btn-primary" style="background: white; color: var(--secondary); box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
          + Buat RAB Baru
        </button>
      </div>
    </header>

    <div class="container">
      <!-- Flash Messages -->
      <div v-if="$page.props.flash.success" class="alert alert-success">
        {{ $page.props.flash.success }}
      </div>
      <div v-if="$page.props.flash.error" class="alert alert-danger">
        {{ $page.props.flash.error }}
      </div>

      <div class="card" style="padding: 0;">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
          <h3 style="font-weight: 600;">Daftar RAB</h3>
        </div>
        <div class="table-container" style="border: none; border-radius: 0 0 var(--radius-lg) var(--radius-lg);">
          <table class="table">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Nama Proyek</th>
                <th>Nama Pelanggan</th>
                <th style="text-align: right;">Total Biaya</th>
                <th style="text-align: center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="rabs.length === 0">
                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                  Belum ada RAB yang dibuat.
                </td>
              </tr>
              <tr v-for="rab in rabs" :key="rab.id">
                <td>{{ formatDate(rab.date) }}</td>
                <td style="font-weight: 500;">{{ rab.project_name }}</td>
                <td>{{ rab.customer_name }}</td>
                <td style="text-align: right; font-weight: 600; color: var(--primary);">
                  {{ formatCurrency(rab.total_amount) }}
                </td>
                <td style="text-align: center;">
                  <a :href="`/rab/${rab.id}/report`" target="_blank" class="badge badge-success" style="margin-right: 0.5rem; padding: 0.4rem 0.8rem; cursor: pointer;">Cetak</a>
                  <button @click="deleteRab(rab.id)" class="badge badge-danger" style="padding: 0.4rem 0.8rem; border: none; cursor: pointer;">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Buat RAB -->
    <div v-if="isModalOpen" class="modal-backdrop">
      <div class="modal-card card">
        <h3 style="margin-bottom: 1.5rem; font-weight: 600; font-size: 1.25rem;">Buat RAB Baru</h3>
        <form @submit.prevent="submit">
          <div class="grid-modal" style="margin-bottom: 1.5rem;">
            <div class="form-group" style="margin: 0;">
              <label class="form-label">Nama Proyek</label>
              <input type="text" class="form-input" v-model="form.project_name" required placeholder="Contoh: Pembuatan Kanopi">
            </div>
            <div class="form-group" style="margin: 0;">
              <label class="form-label">Nama Pelanggan</label>
              <input type="text" class="form-input" v-model="form.customer_name" required placeholder="Contoh: Bpk. Budi">
            </div>
          </div>
          
          <div class="form-group">
            <label class="form-label">Tanggal</label>
            <input type="date" class="form-input" v-model="form.date" required>
          </div>

          <h4 style="margin: 2rem 0 1rem 0; font-weight: 600; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">Item Pekerjaan / Material</h4>
          
          <div v-for="(item, index) in form.items" :key="index" class="grid-items">
            <div class="form-group" style="margin: 0;">
              <label class="form-label" style="font-size: 0.8rem;" v-if="index === 0">Deskripsi</label>
              <input type="text" class="form-input" v-model="item.description" required placeholder="Contoh: Besi Hollow 4x4">
            </div>
            <div class="form-group" style="margin: 0;">
              <label class="form-label" style="font-size: 0.8rem;" v-if="index === 0">Qty</label>
              <input type="number" class="form-input" v-model="item.quantity" min="1" required>
            </div>
            <div class="form-group" style="margin: 0;">
              <label class="form-label" style="font-size: 0.8rem;" v-if="index === 0">Harga Satuan (Rp)</label>
              <input type="text" class="form-input" v-model="displayAmount[index]" @input="e => formatInputAmount(e, index)" required placeholder="100.000">
            </div>
            <div>
              <button type="button" @click="removeItem(index)" class="btn btn-danger" style="padding: 0.75rem; width: 100%;" v-if="form.items.length > 1">Hapus</button>
            </div>
          </div>

          <button type="button" @click="addItem" class="btn" style="background: #f1f5f9; color: var(--text-main); margin-bottom: 2rem; width: 100%;">
            + Tambah Item
          </button>

          <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <button type="button" @click="isModalOpen = false" class="btn" style="background: white; border: 1px solid var(--border-color); color: var(--text-main);">Batal</button>
            <button type="submit" class="btn btn-primary" :disabled="form.processing">Simpan RAB</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style>
.modal-backdrop {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 50;
  padding: 1rem;
}
.modal-card {
  width: 100%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
}
</style>

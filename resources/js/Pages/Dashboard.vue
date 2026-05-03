<script setup>
import { ref } from 'vue';
import { useForm, router, Link } from '@inertiajs/vue3';

const props = defineProps({
  transactions: Array,
  summary: Object,
  currentFilter: String
});

const form = useForm({
  date: new Date().toLocaleDateString('en-CA'),
  type: 'income',
  amount: '',
  description: ''
});

const isEditModalOpen = ref(false);
const editDisplayAmount = ref('');

const editForm = useForm({
  id: null,
  date: '',
  type: 'income',
  amount: '',
  description: ''
});

const displayAmount = ref('');

const formatInputAmount = (e) => {
  let val = e.target.value.replace(/\D/g, '');
  if (val === '') {
    displayAmount.value = '';
    form.amount = '';
    return;
  }
  form.amount = val;
  displayAmount.value = parseInt(val, 10).toLocaleString('id-ID');
};

const submit = () => {
  form.post('/transactions', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('amount', 'description');
      displayAmount.value = '';
    }
  });
};

const openEditModal = (tx) => {
  editForm.id = tx.id;
  editForm.date = tx.date;
  editForm.type = tx.type;
  editForm.amount = tx.amount;
  editForm.description = tx.description;
  editDisplayAmount.value = parseInt(tx.amount, 10).toLocaleString('id-ID');
  isEditModalOpen.value = true;
};

const formatEditInputAmount = (e) => {
  let val = e.target.value.replace(/\D/g, '');
  if (val === '') {
    editDisplayAmount.value = '';
    editForm.amount = '';
    return;
  }
  editForm.amount = val;
  editDisplayAmount.value = parseInt(val, 10).toLocaleString('id-ID');
};

const updateTransaction = () => {
  editForm.put(`/transactions/${editForm.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      isEditModalOpen.value = false;
    }
  });
};

const deleteTransaction = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
    router.delete(`/transactions/${id}`, { preserveScroll: true });
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
        <svg xmlns="http://www.w3.org/-2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-briefcase"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
        Manajemen Keuangan Bengkel Las
      </div>
      <div>
        <Link href="/rab" class="btn" style="color: white; margin-right: 1rem;">Buat RAB</Link>
        <a :href="`/report?filter=${currentFilter}`" target="_blank" class="btn btn-primary" style="background: white; color: var(--secondary); box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
          Cetak PDF Laporan
        </a>
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

      <div class="tabs">
        <Link href="/" :data="{ filter: 'all' }" class="tab" :class="{ 'active': currentFilter === 'all' }">Semua Transaksi</Link>
        <Link href="/" :data="{ filter: 'this_month' }" class="tab" :class="{ 'active': currentFilter === 'this_month' }">Bulan Ini</Link>
        <Link href="/" :data="{ filter: 'last_month' }" class="tab" :class="{ 'active': currentFilter === 'last_month' }">Bulan Lalu</Link>
      </div>

      <!-- Summary Cards -->
      <div class="grid" style="margin-bottom: 2.5rem;">
        <div class="stat-card stat-income">
          <div class="stat-label">Total Pemasukan</div>
          <div class="stat-value">{{ formatCurrency(summary.totalIncome) }}</div>
        </div>
        <div class="stat-card stat-expense">
          <div class="stat-label">Total Pengeluaran</div>
          <div class="stat-value">{{ formatCurrency(summary.totalExpense) }}</div>
        </div>
        <div class="stat-card stat-balance">
          <div class="stat-label">Saldo Saat Ini</div>
          <div class="stat-value">{{ formatCurrency(summary.balance) }}</div>
        </div>
      </div>

      <div class="grid-sidebar">
        <!-- Add Transaction Form -->
        <div class="card" style="align-self: start;">
          <h3 style="margin-bottom: 1.5rem; font-weight: 600;">Tambah Transaksi</h3>
          <form @submit.prevent="submit">
            <div class="form-group">
              <label class="form-label">Tanggal</label>
              <input type="date" class="form-input" v-model="form.date" required>
            </div>
            
            <div class="form-group">
              <label class="form-label">Jenis Transaksi</label>
              <select class="form-input" v-model="form.type" required>
                <option value="income">Pemasukan</option>
                <option value="expense">Pengeluaran</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Nominal (Rp)</label>
              <input type="text" class="form-input" v-model="displayAmount" @input="formatInputAmount" required placeholder="Contoh: 150.000">
            </div>

            <div class="form-group">
              <label class="form-label">Keterangan</label>
              <textarea class="form-input" v-model="form.description" rows="3" required placeholder="Contoh: Pembayaran DP Pagar Besi"></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;" :disabled="form.processing">
              Simpan Transaksi
            </button>
          </form>
        </div>

        <!-- Transactions List -->
        <div class="card">
          <h3 style="margin-bottom: 1.5rem; font-weight: 600;">Riwayat Transaksi</h3>
          <div class="table-container">
            <table class="table">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Keterangan</th>
                  <th>Jenis</th>
                  <th style="text-align: right;">Nominal</th>
                  <th style="text-align: center;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="transactions.length === 0">
                  <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                    Belum ada transaksi.
                  </td>
                </tr>
                <tr v-for="tx in transactions" :key="tx.id">
                  <td>{{ formatDate(tx.date) }}</td>
                  <td>{{ tx.description }}</td>
                  <td>
                    <span v-if="tx.type === 'income'" class="badge badge-success">Pemasukan</span>
                    <span v-else class="badge badge-danger">Pengeluaran</span>
                  </td>
                  <td style="text-align: right; font-weight: 500;" :style="{ color: tx.type === 'income' ? 'var(--success)' : 'var(--danger)' }">
                    {{ tx.type === 'income' ? '+' : '-' }} {{ formatCurrency(tx.amount) }}
                  </td>
                  <td style="text-align: center;">
                    <button @click="openEditModal(tx)" class="badge" style="background: #e2e8f0; color: var(--text-main); padding: 0.25rem 0.5rem; font-size: 0.75rem; border: none; cursor: pointer; margin-right: 0.5rem;">
                      Edit
                    </button>
                    <button @click="deleteTransaction(tx.id)" class="badge badge-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; border: none; cursor: pointer;">
                      Hapus
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Edit Transaksi -->
    <div v-if="isEditModalOpen" class="modal-backdrop">
      <div class="modal-card card" style="max-width: 500px;">
        <h3 style="margin-bottom: 1.5rem; font-weight: 600; font-size: 1.25rem;">Edit Transaksi</h3>
        <form @submit.prevent="updateTransaction">
          <div class="form-group">
            <label class="form-label">Tanggal</label>
            <input type="date" class="form-input" v-model="editForm.date" required>
          </div>
          
          <div class="form-group">
            <label class="form-label">Jenis Transaksi</label>
            <select class="form-input" v-model="editForm.type" required>
              <option value="income">Pemasukan</option>
              <option value="expense">Pengeluaran</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Nominal (Rp)</label>
            <input type="text" class="form-input" v-model="editDisplayAmount" @input="formatEditInputAmount" required placeholder="Contoh: 150.000">
          </div>

          <div class="form-group">
            <label class="form-label">Keterangan</label>
            <textarea class="form-input" v-model="editForm.description" rows="3" required placeholder="Contoh: Pembayaran DP Pagar Besi"></textarea>
          </div>

          <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
            <button type="button" @click="isEditModalOpen = false" class="btn" style="background: white; border: 1px solid var(--border-color); color: var(--text-main);">Batal</button>
            <button type="submit" class="btn btn-primary" :disabled="editForm.processing">Simpan Perubahan</button>
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

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { createColumnHelper, useVueTable, getCoreRowModel, flexRender } from '@tanstack/vue-table'
import axios from '@/libs/axios'
import Swal from 'sweetalert2'

interface Transaksi {
  id: number
  pelanggan: { name: string }
  mitra: { nama_laundry: string }
  kurir?: { nama: string } | null
  layanan: { nama_layanan: string }
  total_harga: number
  status: string
}

const data = ref<Transaksi[]>([])
const loading = ref(false)

const fetchData = async () => {
  loading.value = true
  try {
    const res = await axios.get('/transaksi')
    data.value = res.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchData())

const columnHelper = createColumnHelper<Transaksi>()

const columns = [
  columnHelper.accessor('id', {
    header: 'ID',
    cell: info => info.getValue(),
  }),
  columnHelper.accessor(row => row.pelanggan?.name, {
    id: 'pelanggan',
    header: 'Pelanggan',
    cell: info => info.getValue() || '-' ,
  }),
  columnHelper.accessor(row => row.mitra?.nama_laundry, {
    id: 'mitra',
    header: 'Mitra',
    cell: info => info.getValue() || '-',
  }),
  columnHelper.accessor(row => row.kurir?.nama, {
    id: 'kurir',
    header: 'Kurir',
    cell: info => info.getValue() || 'Belum ditugaskan',
  }),
  columnHelper.accessor(row => row.layanan?.nama_layanan, {
    id: 'layanan',
    header: 'Layanan',
    cell: info => info.getValue(),
  }),
  columnHelper.accessor('total_harga', {
    header: 'Total',
    cell: info => 'Rp ' + info.getValue().toLocaleString(),
  }),
  columnHelper.accessor('status', {
    header: 'Status',
    cell: info => info.getValue(),
  }),
  columnHelper.display({
    id: 'aksi',
    header: 'Aksi',
    cell: ({ row }) => {
      return `<button data-id="${row.original.id}" class="btn-detail">Detail</button>`
    }
  })
]

const table = useVueTable({
  data: data.value,
  columns,
  getCoreRowModel: getCoreRowModel(),
})
</script>

<template>
  <div class="p-4 bg-white shadow rounded-xl">
    <h2 class="text-xl font-bold mb-4">Daftar Transaksi</h2>

    <div v-if="loading" class="text-center py-4">Loading...</div>

    <table v-else class="w-full border rounded-lg overflow-hidden">
      <thead class="bg-gray-100">
        <tr>
          <th v-for="header in table.getHeaderGroups()[0].headers" :key="header.id" class="p-2 border">
            {{ flexRender(header.column.columnDef.header, header.getContext()) }}
          </th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="row in table.getRowModel().rows" :key="row.id" class="hover:bg-gray-50">
          <td v-for="cell in row.getVisibleCells()" :key="cell.id" class="p-2 border">
            <span v-html="flexRender(cell.column.columnDef.cell, cell.getContext())"></span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.btn-detail {
  background: #4f46e5;
  color: white;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 12px;
}
.btn-detail:hover {
  background: #4338ca;
}
</style>

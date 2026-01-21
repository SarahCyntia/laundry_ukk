<script setup lang="ts">
import { computed, onMounted, ref, watch, nextTick } from "vue";
import { createColumnHelper, type Row } from "@tanstack/vue-table";
import type { Order } from "@/types";
import { useDelete } from "@/libs/hooks";
import { h } from "vue";
import Form from "./form-order-masuk.vue";
import Swal from "sweetalert2";
import axios from "@/libs/axios";
import { saveAs } from 'file-saver';

// const url = "/order";

const mitraId = ref<number | null>(null);
const column = createColumnHelper();
// const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);
const order = ref<Order | null>(null); // Data pelanggan yang terkait dengan user login

// AMBIL MITRA_ID DARI PROFILE USER
onMounted(async () => {
  const { data } = await axios.get("/mitra");
  // pastikan API ini mengembalikan user + mitra_id
  mitraId.value = data.mitra_id;
});

const props = defineProps<{ selected: string }>();
const selectedId = ref(null);
const showForm = ref(false);

function openEdit(id) {
  selectedId.value = id;
  showForm.value = true;
}

// const url = "/order-masuk"; // <--- WAJIB ADA

const paginateRef = ref<any>(null);
const refresh = () => paginateRef.value?.refetch();

// const column = createColumnHelper<Order>();

const { delete: deleteOrder } = useDelete({
  onSuccess: () => paginateRef.value.refetch(),
});

const inputData = ref<Order | null>(null);

const statusSteps = [
  "menunggu_konfirmasi_mitra",
  "ditunggu_mitra",
  "diterima",
  "ditolak",
  "diproses",
  "dicuci",
  "dikeringkan",
  "disetrika",
  "siap_diambil",
  "selesai"
] as const;

const statusLabels = {
  menunggu_konfirmasi_mitra: "Menunggu Konfirmasi Mitra",
  ditunggu_mitra: "Ditunggu Mitra",
  diterima: "Diterima",
  ditolak: "Ditolak",
  diproses: "Diproses",
  dicuci: "Dicuci",
  dikeringkan: "Dikeringkan",
  disetrika: "Disetrika",
  siap_diambil: "Siap Diambil",
  selesai: "Selesai"
};

const statusColors = {
  menunggu_konfirmasi_mitra: "bg-info",
  ditunggu_mitra: "bg-info",
  diterima: "bg-success",
  ditolak: "bg-danger",
  diproses: "bg-warning",
  dicuci: "bg-muted",
  dikeringkan: "bg-warning",
  disetrika: "bg-primary",
  siap_diambil: "bg-info",
  selesai: "bg-success"
};

const statusIcons = {
  menunggu_konfirmasi_mitra: "info",
  ditunggu_mitra: "info",
  diterima: "question",
  ditolak: "error",
  diproses: "info",
  dicuci: "info",
  dikeringkan: "warning",
  disetrika: "info",
  siap_diambil: "info",
  selesai: "success"
};








const bayar = async (rowData) => {
  if (rowData.isPaying) return;

  rowData.isPaying = true;
  const draft = {
  order_id: rowData.id,
  berat_estimasi: rowData.berat_barang,
  harga_final: rowData.total_harga,
  biaya: rowData.biaya,
  jenis_layanan_id: rowData.selectedService?.id,
};


// const draft = {
//   id: rowData.id,

//   // Berat & harga
//   berat_estimasi: rowData.berat_estimasi,
//   berat_aktual: rowData.berat_aktual,
//   harga_final: rowData.harga_final,
//   biaya: rowData.biaya,

//   // Layanan
//   jenis_layanan_id: rowData.selectedService?.id ?? rowData.selectedService,

//   // Catatan tambahan (opsional)
//   catatan: rowData.catatan ?? null,
// };


  sessionStorage.setItem("draftTransaksi", JSON.stringify(draft));

  try {
    const res = await axios.post(`/api/payment/token/${row.id}`);
    // const res = await axios.post("/payment/create", draft);
    const { snap_token } = res.data;

    if (!snap_token) throw new Error("Token pembayaran tidak ditemukan.");

    window.snap.pay(snap_token, {
      onSuccess: function (result) {
        console.log("✅ Pembayaran berhasil:", result);
        rowData.status = "paid";
      },
      onPending: function (result) {
        console.log("⏳ Menunggu pembayaran:", result);
        rowData.status = "pending";
      },
      onError: function (result) {
        console.error("❌ Pembayaran gagal:", result);
      },
      onClose: function () {
        console.log("❎ Popup ditutup");
      },
    });
  } catch (err) {
    console.error("❌ Terjadi kesalahan:", err);
  } finally {
    rowData.isPaying = false;
  }
};
const getPembayaranBadgeClass = (status: string | undefined) => {
const map = {
  dibayar: "badge bg-success",
  menunggu_pembayaran: "badge bg-warning text-dark",
  kadaluarsa: "badge bg-secondary",
  dibatalkan: "badge bg-danger",
  dikembalikan: "badge bg-info text-dark",
  belum_dibayar: "badge bg-secondary",
};

    return map[status?.toLowerCase() ?? ""] || "badge bg-secondary fw-bold";
};

// const redirectToPayment = async (id: number) => {
//     try {
//         const { data } = await axios.post(`/payment/token/${id}`);
//         const snapToken = data.snap_token;

//         if (!snapToken) {
//             Swal.fire({ icon: 'error', title: 'Token Tidak Tersedia' });
//             return;
//         }

//         if (typeof window.snap === 'undefined') {
//             Swal.fire({ icon: 'error', title: 'Snap Belum Siap' });
//             return;
//         }

//         window.snap.pay(snapToken, {
//             onSuccess: async (result: any) => {
//                 await axios.post('/manual-update-status', {
//                     order_id: id,
//                     transaction_status: result.transaction_status,
//                     payment_type: result.payment_type
//                 });
//                 // Swal.fire({ icon: 'success', title: 'Pembayaran Berhasil' }).then(() => {
//                 //   refresh();
//                 //   });
//                 Swal.fire({ icon: 'success', title: 'Pembayaran Berhasil' }).then(
//                     refresh()
//                 );
//             },
//             onPending: async (result: any) => {
//                 await axios.post('/manual-update-status', {
//                     order_id: result.order_id,
//                     transaction_status: result.transaction_status,
//                     payment_type: result.payment_type
//                 });
//                 Swal.fire({ icon: 'info', title: 'Menunggu Pembayaran' });
//             },
//             onError: () => {
//                 Swal.fire({ icon: 'error', title: 'Pembayaran Gagal' });
//             },
//             onClose: () => {
//                 Swal.fire({ icon: 'warning', title: 'Dibatalkan' });
//             }
//         });
//     } catch (error) {
//         console.error("❌ Gagal ambil token:", error);
//         Swal.fire({ icon: 'error', title: 'Error mengambil token' });
//     }
// };
const redirectToPayment = async (orderId: number) => {
  try {
    const { data } = await axios.post(`/payment/token/${orderId}`);
    const snapToken = data.snap_token;

    if (!snapToken) {
      Swal.fire("Error", "Snap token tidak tersedia", "error");
      return;
    }

    window.snap.pay(snapToken, {
      onSuccess: () => {
        Swal.fire(
          "Pembayaran diproses",
          "Status akan diperbarui otomatis",
          "info"
        );
        refresh();
      },

      onPending: () => {
        Swal.fire(
          "Menunggu pembayaran",
          "Silakan selesaikan pembayaran",
          "info"
        );
        refresh();
      },

      onError: () => {
        Swal.fire("Gagal", "Pembayaran gagal", "error");
      },

      onClose: () => {
        Swal.fire("Dibatalkan", "Popup ditutup", "warning");
      }
    });
  } catch (error) {
    console.error(error);
    Swal.fire("Error", "Gagal mengambil snap token", "error");
  }
};







const url = computed(() => {
  const params = new URLSearchParams();

  // Status yang tidak ditampilkan
  [
    'diproses',
    'dicuci',
    'dikeringkan',
    'disetrika',
    'siap_ambil',
    'selesai'
  ].forEach(status => {
    params.append('exclude_status[]', status);
  });

  return `/order?${params.toString()}`;
});





const updateStatus = async (row: Row<Order>) => {
  const currentStatus = row.original.status;
  const currentIndex = statusSteps.indexOf(currentStatus);
  if (currentIndex === -1 || currentStatus === "selesai") return;

  const nextStatus = statusSteps[currentIndex + 1];

  const confirmed = await Swal.fire({
    icon: statusIcons[nextStatus],
    title: `Ubah Status ke "${statusLabels[nextStatus]}"?`,
    showCancelButton: true
  }).then(r => r.isConfirmed);

  if (!confirmed) return;

  await axios.put(`/order/${row.original.id}/status`, {
    status: nextStatus
  });

  Swal.fire("Berhasil", "Status diperbarui", "success");
  await refresh();
};





const downloadReceipt = async (noKode: string) => {
  const response = await axios.get(`/download-struk/${noKode}`, {
    responseType: 'blob'

  });

  saveAs(response.data, `struk-${noKode}.pdf`);
};

const noKode = ref("");

const columns = [
  column.accessor("no", { header: "No" }),
  // column.accessor("pelanggan.user.name", { header: "Nama Pelanggan" }),
  column.accessor(row => row.pelanggan?.name ?? "-", {
    header: "Nama Pelanggan",
  }),

  column.accessor("mitra.nama_laundry", { header: "Nama Laundry" }),
  column.accessor("jenis_layanan.nama_layanan", { header: "Jenis Layanan" }),


  //   column.accessor("pelanggan.user.name", {
  //   header: "Nama Pelanggan",
  //   cell: ({ row }) => row.original.pelanggan?.user?.name ?? "-",
  // }),
  //   column.accessor("mitra_id", { header: "Nama Laundry" }),
  //   // column.accessor("jenis_layanan_id", { header: "Jenis Layanan" }),
  //   column.accessor("jenis_layanan.nama_layanan", {
  //     header: "Jenis Layanan",
  //     cell: ({ row }) => row.original.jenis_layanan?.nama_layanan ?? "-",
  //   }),
  column.accessor("kode_order", { header: "Kode Order" }),
  column.accessor("berat_estimasi", { header: "Berat Estimasi" }),
  column.accessor("berat_aktual", { header: "Berat Aktual" }),
  column.accessor("harga_final", { header: "Harga" }),
  column.accessor("catatan", { header: "Catatan" }),
  // column.accessor("alasan_penolakan", { header: "Alasan Penolakan" }),
  column.accessor("alasan_penolakan", {
    header: "Alasan Penolakan",
    cell: ({ getValue, row }) => {
      const alasan = getValue();

      // Kalau status diterima / bukan ditolak
      if (!alasan && row.original.status !== "ditolak") {
        return h(
          "span",
          { style: "color:#888; font-style:italic;" },
          "Tidak ada alasan penolakan"
        );
      }
      return alasan || "-";
    },
  }),

  // column.accessor("waktu_pelanggan_antar", { header: "Waktu Antar" }),
  column.accessor("waktu_pelanggan_antar", {
    header: "Waktu Antar",
    cell: ({ getValue }) => {
      const val = getValue()
      return val
        ? new Date(val).toLocaleString("id-ID")
        : "-"
    }
  }),

  column.accessor("waktu_diambil", { header: "Waktu Diambil" }),
  column.accessor("foto_struk", {
    header: "Foto Struk",
    cell: ({ getValue }) => {
      const foto = getValue();

      if (!foto) {
        return h("span", { style: "color:#888;" }, "Tidak ada foto");
      }

      const url = `http://localhost:8000/storage/${foto}`;


      console.log("URL FINAL:", url);

      return h("img", {
        src: url,
        style: "width: 80px; height: 80px; object-fit: cover; border-radius: 8px;",
      });
    }
  }),


  column.accessor("status", {
    header: "Status",
    cell: ({ row }) => {
      const val = row.original.status;
      const color = statusColors[val] || "bg-secondary";

      return h(
        "button",
        {
          class: `badge ${color} text-white border-0 cursor-pointer`,
          onClick: () => updateStatus(row)
        },
        val.replaceAll("_", " ")
      );
    }
  }),
  column.accessor("id", {
    header: "Aksi",
    cell: (cell) => {
      const row = cell.row.original;
      const actions: any[] = [];

    
      // === Jika status masih menunggu konfirmasi mitra ===
      if (row.status === "menunggu_konfirmasi_mitra") {
        // Terima
        actions.push(
          h(
            "button",
            {
              class: "btn btn-sm btn-success",
              onClick: async () => {
                const ok = await Swal.fire({
                  icon: "question",
                  title: "Terima order ini?",
                  showCancelButton: true,
                }).then((r) => r.isConfirmed);

                if (!ok) return;

                await axios.post(`/order/${row.id}/konfirmasi`, {
                  status: "ditunggu_mitra",
                });

                Swal.fire("Berhasil", "Order diterima!", "success");
                await refresh();
              },
            },
            "Terima"
          )
        );

        // Tolak
        actions.push(
          h(
            "button",
            {
              class: "btn btn-sm btn-danger",
              onClick: async () => {
                const { value: alasan } = await Swal.fire({
                  title: "Alasan penolakan",
                  input: "text",
                  inputPlaceholder: "Tulis alasan...",
                  showCancelButton: true,
                });

                if (!alasan) return;

                await axios.post(`/order/${row.id}/tolak`, {
                  status: "ditolak",
                  alasan_penolakan: alasan,
                });

                Swal.fire("Ditolak", "Order berhasil ditolak", "success");
                await refresh();
              },
            },
            "Tolak"
          )
        );
      }

      // === Jika status diterima → Edit ===
      if (row.status?.trim() === "diterima") {
        actions.push(
          h(
            "button",
            {
              class: "btn btn-sm btn-icon btn-info",
              onClick: () => {
                selected.value = cell.getValue();
                openForm.value = true;
              },
            },
            h("i", { class: "la la-pencil fs-2" })
          )
        );
      }

      // === Tombol Hapus (selalu ada) ===
      actions.push(
        h(
          "button",
          {
            class: "btn btn-sm btn-icon btn-danger",
            onClick: () => deleteOrder(`order/${cell.getValue()}`),
          },
          h("i", { class: "la la-trash fs-2" })
        ),
      );

      // 
      return h(
        "div",
        { class: "d-flex gap-2 flex-nowrap align-items-center" },
        actions
      );
    },

    
  }),

column.display({
  id: "struk",
  header: "Struk",
  cell: ({ row }) => {
    const noKode = row.original.kode_order;

    return h("div", { class: "d-flex gap-2" }, [
    
      h(
        "button",
        {
          class: "btn btn-sm btn-secondary",
          onClick: () => downloadReceipt(noKode),
          title: "Download PDF"
        },
        [
          h("i", { class: "la la-download me-1" }),
          "Download"
        ]
      )
    ]);
  },
}),

column.display({
  id: "paymentAction",
  header: "Pembayaran",
cell: ({ row }) => {
  const status = row.original.transaksi?.status_pembayaran;

  if (status === "dibayar") {
    return h("span", { class: "badge bg-success" }, "Lunas");
  }

  return h(
    "button",
    {
      class: "btn btn-sm btn-success",
      onClick: () => redirectToPayment(row.original.id),
    },
    [h("i", { class: "bi bi-credit-card me-1" }), "Bayar"]
  );
},
}),
column.accessor(
  row => row.transaksi?.status_pembayaran ?? 'belum_dibayar',
  {
    header: "Status Bayar",
    cell: ({ getValue }) => {
      const status = getValue();

      const map: Record<string, string> = {
        dibayar: "badge bg-success",
        menunggu_pembayaran: "badge bg-warning text-dark",
        kadaluarsa: "badge bg-secondary",
        dibatalkan: "badge bg-danger",
        dikembalikan: "badge bg-info text-dark",
        belum_dibayar: "badge bg-secondary",
      };

      return h(
        "span",
        { class: map[status] },
        status.replaceAll("_", " ")
      );
    }
  }
),

];
onMounted(() => {
  if (!window.snap) {
    const script = document.createElement("script");
    script.src = "https://app.sandbox.midtrans.com/snap/snap.js";
    script.setAttribute(
      "data-client-key",
      import.meta.env.VITE_MIDTRANS_CLIENT_KEY
    );
    script.async = true;
    document.body.appendChild(script);
  }
});
// onMounted(refresh);
onMounted(async () => {
  await nextTick();
  refresh();
});

</script>


<template>


  <Form v-if="openForm" :selected="selected" @close="openForm = false" @refresh="refresh" />
  <div class="card">
    <div class="card-header align-items-center">
      <h2 class="mb-0">Orderan</h2>
    </div>
    <!-- <paginate ref="paginateRef" :url="`/order-masuk`" :columns="columns" /> -->

    <paginate ref="paginateRef" :url="url" :columns="columns" />


    <!-- <paginate ref="paginateRef" id="table-order" :url="url" :columns="columns" /> -->
  </div>
</template>

<style scoped>
.btn {
  margin-top: 1rem;
  padding: 0.5rem 1.5rem;
}
</style>
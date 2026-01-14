<script setup lang="ts">
import { computed, onMounted, ref, watch, nextTick } from "vue";
import { createColumnHelper, type Row } from "@tanstack/vue-table";
import type { Order } from "@/types";
import { useDelete } from "@/libs/hooks";
import { h } from "vue";
import Form from "./form-order-masuk.vue";
import Swal from "sweetalert2";
import axios from "@/libs/axios";

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







const redirectToPayment = async (id: number) => {
    try {
        const { data } = await axios.get(`/payment/token/${id}`);
        const snapToken = data.snap_token;

        if (!snapToken) {
            Swal.fire({ icon: 'error', title: 'Token Tidak Tersedia' });
            return;
        }

        if (typeof window.snap === 'undefined') {
            Swal.fire({ icon: 'error', title: 'Snap Belum Siap' });
            return;
        }

        window.snap.pay(snapToken, {
            ...paymentCallbacks, // Callback ditentukan di bawah
            onSuccess: async (result: any) => {
                await axios.post('/manual-update-status', {
                    order_id: result.order_id,
                    transaction_status: result.transaction_status,
                    payment_type: result.payment_type
                });
                Swal.fire({ icon: 'success', title: 'Pembayaran Berhasil' }).then(
                    refresh()
                );
            },
        });
    } catch (error) {
        console.error("❌ Gagal ambil token:", error);
        Swal.fire({ icon: 'error', title: 'Error mengambil token' });
    }
};

// Callback Midtrans Snap untuk berbagai status pembayaran
const paymentCallbacks = {
    onSuccess: async (result: any) => {
        console.log("✅ Pembayaran berhasil:", result);
        await axios.post('/manual-update-status', {
            order_id: result.order_id,
            transaction_status: result.transaction_status,
            payment_type: result.payment_type
        });
        await Swal.fire({
            icon: 'success',
            title: 'Pembayaran Berhasil',
            text: 'Terima kasih, pembayaran Anda telah berhasil.',
        });
    },
    onPending: async (result: any) => {
        await axios.post('/manual-update-status', {
            order_id: result.order_id,
            transaction_status: result.transaction_status,
            payment_type: result.payment_type
        });
        await Swal.fire({
            icon: 'info',
            title: 'Menunggu Pembayaran',
            text: 'Pembayaran sedang menunggu penyelesaian.',
        });
    },
    onError: (result: any) => {
        console.error("❌ Terjadi kesalahan saat pembayaran:", result);
        Swal.fire({
            icon: 'error',
            title: 'Pembayaran Gagal',
            text: 'Terjadi kesalahan saat memproses pembayaran.',
        });
    },
    onClose: () => {
        console.warn("❗ Pembayaran dibatalkan oleh pengguna.");
        Swal.fire({
            icon: 'warning',
            title: 'Pembayaran Dibatalkan',
            text: 'Anda telah membatalkan proses pembayaran.',
        });
    }
};

// Fungsi untuk menentukan class badge berdasarkan status pembayaran
const getPembayaranBadgeClass = (status: string | undefined) => {
    const statusMap: Record<string, string> = {
        settlement: "badge bg-success fw-bold",
        pending: "badge bg-warning text-dark fw-bold",
        expire: "badge bg-secondary fw-bold",
        cancel: "badge bg-dark fw-bold",
        deny: "badge bg-danger fw-bold",
        failure: "badge bg-danger fw-bold",
        refund: "badge bg-info text-dark fw-bold",
        "belum di bayar": "badge bg-danger fw-bold",
    };

    return statusMap[status?.toLowerCase() ?? ""] || "badge bg-secondary fw-bold";
};
const snapLoaded = ref(false);
onMounted(() => {
    if (!window.snap) {
        const script = document.createElement("script");
        script.src = "https://app.sandbox.midtrans.com/snap/snap.js";
        script.setAttribute("data-client-key", "SB-Mid-client-JuHAlpsbUGhh4cvF"); // Ganti dengan client key produksi di deployment
        script.async = true;
        script.onload = () => {
            snapLoaded.value = true;
        };
        document.body.appendChild(script);
    } else {
        snapLoaded.value = true;
    }
    //  stopAutoRefresh();
});

const pollingInterval = ref<number | null>(null);

const startAutoRefresh = () => {
  // Jalankan pertama kali
  refresh();

  // Set interval polling setiap 5 detik (5000ms)
  pollingInterval.value = setInterval(() => {
    refresh(); // Memanggil fungsi refresh tabel
  }, 5000);
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
//     column.accessor("id", {
//   header: "Aksi",
//   cell: (cell) => {
//   const row = cell.row.original;
//   const actions = [];

//   // === Jika status masih menunggu konfirmasi ===
//   if (row.status === "menunggu_konfirmasi_mitra") {
//     actions.push(
//       h(
//         "button",
//         {
//           class: "btn btn-sm btn-success",
//           onClick: async () => {
//             const ok = await Swal.fire({
//               icon: "question",
//               title: "Terima order ini?",
//               showCancelButton: true
//             }).then(r => r.isConfirmed);

//             if (!ok) return;

//             await axios.post(`/order/${row.id}/konfirmasi`, {
//               status: "ditunggu_mitra"
//             });

//             Swal.fire("Berhasil", "Order diterima!", "success");
//             await refresh();
//           },
//         },
//         "Terima"
//       )
//     );

//     // Tombol Tolak
//     actions.push(
//       h(
//         "button",
//         {
//           class: "btn btn-sm btn-danger",
//           onClick: async () => {
//             const { value: alasan } = await Swal.fire({
//               title: "Alasan penolakan",
//               input: "text",
//               inputPlaceholder: "Tulis alasan...",
//               showCancelButton: true,
//             });

//             if (!alasan) return;

//             await axios.post(`/order/${row.id}/tolak`, {
//               status: "ditolak",
//               alasan_penolakan: alasan
//             });

//             Swal.fire("Ditolak", "Order berhasil ditolak", "success");
//             await refresh();
//           },
//         },
//         "Tolak"
//       )
//     );
//   }

//   // === Jika status ditolak -> hanya bisa hapus ===
//   // if (row.status !== "ditolak") {
//     if (row.status && row.status.trim() === "diterima") {
//     // Tombol Edit
//     actions.push(
//       h(
//         "button",
//         {
//           class: "btn btn-sm btn-icon btn-info",
//           onClick: () => {
//             selected.value = cell.getValue();
//             openForm.value = true;
//           },
//         },
//         h("i", { class: "la la-pencil fs-2" })
//       )
//     );
//   }

//   // === Tombol hapus tetap ada untuk semua kecuali selesai (opsional) ===
//   actions.push(
//     h(
//       "button",
//       {
//         class: "btn btn-sm btn-icon btn-danger",
//         onClick: () => deleteOrder(`order/${cell.getValue()}`),
//       },
//       h("i", { class: "la la-trash fs-2" })
//     )
//   );

//   return h("div", { class: "d-flex gap-2" }, actions);
// },



//   // cell: (cell) => {
//   //   const row = cell.row.original;
//   //   const actions = [];

//   //   // === Tombol TERIMA kalau status menunggu konfirmasi ===
//   //   if (row.status === "menunggu_konfirmasi_mitra") {
//   //     actions.push(
//   //       h(
//   //         "button",
//   //         {
//   //           class: "btn btn-sm btn-success",
//   //           onClick: async () => {
//   //             const ok = await Swal.fire({
//   //               icon: "question",
//   //               title: "Terima order ini?",
//   //               showCancelButton: true
//   //             }).then(r => r.isConfirmed);

//   //             if (!ok) return;

//   //             await axios.post(`/order/${row.id}/konfirmasi`, {
//   //               status: "diterima"
//   //             });

//   //             Swal.fire("Berhasil", "Order diterima!", "success");
//   //             await refresh();
//   //           },
//   //         },
//   //         "Terima"
//   //       )
//   //     );

//   //     // === Tombol TOLAK ===
//   //     actions.push(
//   //       h(
//   //         "button",
//   //         {
//   //           class: "btn btn-sm btn-danger",
//   //           onClick: async () => {
//   //             const { value: alasan } = await Swal.fire({
//   //               title: "Alasan penolakan",
//   //               input: "text",
//   //               inputPlaceholder: "Tulis alasan...",
//   //               showCancelButton: true,
//   //             });

//   //             if (!alasan) return;

//   //             await axios.post(`/order/${row.id}/tolak`, {
//   //               status: "ditolak",
//   //               alasan_penolakan: alasan
//   //             });

//   //             Swal.fire("Ditolak", "Order berhasil ditolak", "success");
//   //             await refresh();
//   //           },
//   //         },
//   //         "Tolak"
//   //       )
//   //     );
//   //   }

//   //   // === Tombol Edit ===
//   //   actions.push(
//   //     h(
//   //       "button",
//   //       {
//   //         class: "btn btn-sm btn-icon btn-info",
//   //         onClick: () => {
//   //           selected.value = cell.getValue();
//   //           openForm.value = true;
//   //         },
//   //       },
//   //       h("i", { class: "la la-pencil fs-2" })
//   //     )
//   //   );

//   //   // === Tombol Hapus ===
//   //   actions.push(
//   //     h(
//   //       "button",
//   //       {
//   //         class: "btn btn-sm btn-icon btn-danger",
//   //         onClick: () => deleteOrder(`order/${cell.getValue()}`),
//   //       },
//   //       h("i", { class: "la la-trash fs-2" })
//   //     )
//   //   );

//   //   return h("div", { class: "d-flex gap-2" }, actions);
//   // },
// }),
column.accessor("id", {
  header: "Aksi",
  cell: (cell) => {
    const row = cell.row.original;
    const actions: any[] = [];

    const statusPembayaran = row.status_pembayaran?.toLowerCase();

    // === Tombol Bayar (jika belum settlement) ===
    if (statusPembayaran !== "settlement") {
      actions.push(
        h(
          "button",
          {
            class: "btn btn-sm btn-success me-1",
            onClick: () => redirectToPayment(row.id),
          },
          [h("i", { class: "bi bi-credit-card me-1" }), "Bayar"]
        )
      );
    }

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
      )
    );

    // 
    return h(
  "div",
  { class: "d-flex gap-2 flex-nowrap align-items-center" },
  actions
);
  },
}),





];
onMounted(() => {
    if (!window.snap) {
        const script = document.createElement("script");
        script.src = "https://app.sandbox.midtrans.com/snap/snap.js";
        script.setAttribute("data-client-key", "SB-Mid-client-XXXXX"); // ganti sesuai client key kamu
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
     
<paginate
  ref="paginateRef"
  :url="url"
  :columns="columns"
/>


    <!-- <paginate ref="paginateRef" id="table-order" :url="url" :columns="columns" /> -->
  </div>
</template>

<style scoped>
.btn {
  margin-top: 1rem;
  padding: 0.5rem 1.5rem;
}
</style>
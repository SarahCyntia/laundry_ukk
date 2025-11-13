<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { Mitra } from "@/types";
// import type { mitra } from "@/types";
import axios from "axios";
import Swal from "sweetalert2";  

// const column = createColumnHelper<mitra>();
const column = createColumnHelper<Mitra>();
const paginateRef = ref<any>(null);
const trashRef    = ref<any>(null);
const selected    = ref<string>("");
const openForm    = ref<boolean>(false);
const mitraData = ref<Mitra | null>(null);


// hapus data aktif
const { delete: deleteMitra } = useDelete({
  onSuccess: () => refresh(),
});

// hapus permanen (sampah)
const { delete: forceDeleteMitra } = useDelete({
  onSuccess: () => trashRef.value.refetch(),
});

// restore data
const restoreMitra = (url: string) => {
  Swal.fire({
    title: "Pulihkan data?",
    text: "Data akan dikembalikan ke daftar utama.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Ya, pulihkan",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      axios.post(url).then(() => {
        Swal.fire({
          icon: "success",
          title: "Berhasil",
          text: "Data berhasil dipulihkan!",
          timer: 1500,
          showConfirmButton: false,
        });
        refresh();
      }).catch(() => {
        Swal.fire({
          icon: "error",
          title: "Gagal",
          text: "Terjadi kesalahan saat memulihkan data.",
        });
      });
    }
  });
};


const updateStatus = (id: number, status: string) => {
  const label =
    status === "aktif" ? "Terima" : status === "ditolak" ? "Tolak" : "Ubah";

  Swal.fire({
    title: `${label} mitra ini?`,
    text: `Status akan diubah menjadi ${status}.`,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: `Ya, ${label}`,
    cancelButtonText: "Batal",
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await axios.put(`/mitra/${id}/status`, { status });
        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: `Mitra berhasil di${status === "aktif" ? "terima" : "tolak"}.`,
          timer: 1500,
          showConfirmButton: false,
        });
        refresh();
      } catch (err) {
        Swal.fire({
          icon: "error",
          title: "Gagal!",
          text: "Terjadi kesalahan saat mengubah status.",
        });
      }
    }
  });
};
// const statusFilter = ref("pending");

const fetchData = async () => {
  const res = await axios.get("/api/mitra/all");
  data.value = res.data;
};

// setelah mitra ubah status
await axios.put(`/mitra`, { status_toko: "tutup" });
await fetchData(); // refresh tabel


// kolom tabel utama
const columns = [
  column.accessor("no", { header: "No" }),
  column.accessor("nama_laundry", { header: "Nama laundry" }),
  column.accessor("user.name", { header: "Nama Pemilik" }),
  column.accessor("user.email", { header: "Email" }),
  column.accessor("user.phone", { header: "No Hp" }),
  column.accessor("alamat_laundry", { header: "Alamat Laundry" }),
  column.accessor("user.photo", {
    header: "Foto",
    cell: (cell) => {
        const val = cell.getValue();
        console.log("Photo path:", val);
        return h("img", {
            src: val ? `/storage/${val}` : "/img/default.png",
            alt: "Foto Mitra",
            class: "img-thumbnail",
            style: "width: 50px; height: 50px;",
        });
    },
}),
  column.accessor("foto_ktp", {
    header: "Foto",
    cell: (cell) => {
        const val = cell.getValue();
        console.log("Photo path:", val);
        return h("img", {
            src: val ? `/storage/${val}` : "/img/default.png",
            alt: "Foto KTP",
            class: "img-thumbnail",
            style: "width: 50px; height: 50px;",
        });
    },
}),
  column.accessor("status_toko", {
  header: "Status Toko",
  cell: ({ getValue }) => {
    const status = getValue() as string;
    const color =
      status === "buka"
        ? "bg-success text-white"
        : "bg-danger text-white";
    return h("span", { class: `px-2 py-1 rounded ${color}` }, status.toUpperCase());
  },
}),
  column.accessor("alamat_laundry", { header: "Alamat Laundry" }),
  // column.accessor("password", { header: "Password" }),
  // column.accessor("status", { header: "Status" }),
   column.accessor("status", {
    header: "Status",
    cell: (cell) => {
      const status = cell.getValue();
      const id = cell.row.original.id;

      // Pending → tombol Terima / Tolak
      if (status === "pending") {
        return h("div", { class: "d-flex gap-2" }, [
          h(
            "button",
            {
              class: "btn btn-sm btn-success",
              onClick: () => updateStatus(id, "aktif"),
            },
            "Terima"
          ),
          h(
            "button",
            {
              class: "btn btn-sm btn-danger",
              onClick: () => updateStatus(id, "ditolak"),
            },
            "Tolak"
          ),
        ]);
      }

      // Aktif / Ditolak → tampilkan badge
      const color =
        status === "aktif"
          ? "badge-success"
          : status === "ditolak"
          ? "badge-danger"
          : "badge-secondary";

      return h(
        "span",
        { class: `badge ${color}` },
        status.charAt(0).toUpperCase() + status.slice(1)
      );
    },
  }),

//   column.accessor("status", {
//   header: "Status",
//   cell: (cell) => {
//     const status = cell.getValue();
//     const id = cell.row.original.id;

//     // kalau pending, tampilkan tombol Terima & Tolak
//     if (status === "pending") {
//       return h("div", { class: "d-flex gap-2" }, [
//         h(
//           "button",
//           {
//             class: "btn btn-sm btn-success",
//             onClick: () => {
//               axios.post(`/mitra/${id}/update-status`, { status: "aktif" })
//                 .then(() => {
//                   Swal.fire("Berhasil", "Mitra diterima!", "success");
//                   refresh();
//                 })
//                 .catch(() => Swal.fire("Error", "Gagal mengubah status", "error"));
//             },
//           },
//           "Terima"
//         ),
//         h(
//           "button",
//           {
//             class: "btn btn-sm btn-danger",
//             onClick: () => {
//               axios.post(`/mitra/${id}/update-status`, { status: "ditolak" })
//                 .then(() => {
//                   Swal.fire("Ditolak", "Mitra ditolak!", "info");
//                   refresh();
//                 })
//                 .catch(() => Swal.fire("Error", "Gagal mengubah status", "error"));
//             },
//           },
//           "Tolak"
//         ),
//       ]);
//     }

//     // kalau sudah active / rejected tampilkan badge
//     const color =
//       status === "aktif"
//         ? "badge-success"
//         : status === "ditolak"
//         ? "badge-danger"
//         : "badge-secondary";

//     return h(
//       "span",
//       { class: `badge ${color}` },
//       status.charAt(0).toUpperCase() + status.slice(1)
//     );
//   },
// }),

  column.accessor("id", {
    header: "Aksi",
    cell: (cell) =>
      h("div", { class: "d-flex gap-2" }, [
        h(
          "button",
          {
            class: "btn btn-sm btn-icon btn-info",
            onClick: () => {
              selected.value = String(cell.getValue());
              openForm.value = true;
            },
          },
          h("i", { class: "la la-pencil fs-2" })
        ),
        h(
          "button",
          {
            class: "btn btn-sm btn-icon btn-danger",
            onClick: () => {
              deleteMitra(`/mitra/${cell.getValue()}`);
              trashRef.value.refetch();
            },
          },
          h("i", { class: "la la-trash fs-2" })
        ),
      ]),
  }),
];

// kolom tabel sampah
const trashColumns = [
  column.accessor("nama_laundry", { header: "Nama Mitra" }),
  column.accessor("pemilik", { header: "Pemilik" }),
  column.accessor("email", { header: "Email" }),
  column.accessor("no_hp", { header: "No Hp" }),
  column.accessor("alamat", { header: "alamat" }),
  column.accessor("password", { header: "Password" }),
  column.accessor("status", { header: "Status" }),
  column.accessor("created_at", { header: "Created At" }),
  column.accessor("deleted_at", { header: "Deleted At" }),
  column.accessor("id", {
  header: "Aksi",
  cell: (cell) =>
    h("div", { class: "d-flex gap-2 flex-wrap" }, [
      // ✏️ Edit
      h(
        "button",
        {
          class: "btn btn-sm btn-icon btn-info",
          title: "Edit",
          onClick: () => {
            selected.value = String(cell.getValue());
            openForm.value = true;
          },
        },
        h("i", { class: "la la-pencil fs-2" })
      ),

      // 🗑 Hapus
      h(
        "button",
        {
          class: "btn btn-sm btn-icon btn-danger",
          title: "Hapus",
          onClick: () => {
            deleteMitra(`/mitra/${cell.getValue()}`);
            trashRef.value.refetch();
          },
        },
        h("i", { class: "la la-trash fs-2" })
      ),

      // ✅ Terima Mitra
     h(
  "button",
  {
    class: "btn btn-sm btn-success",
    onClick: () => {
      axios.post(`/mitra/${cell.getValue()}/approve`).then(() => {
        Swal.fire("Berhasil", "Mitra disetujui!", "success");
        refresh();
      });
    },
  },
  "Terima"
),
h(
  "button",
  {
    class: "btn btn-sm btn-danger",
    onClick: () => {
      axios.post(`/mitra/${cell.getValue()}/update-status`, { status: "ditolak" }).then(() => {
        Swal.fire("Ditolak", "Pendaftaran mitra ditolak!", "info");
        refresh();
      });
    },
  },
  "Tolak"
)

    ]),
}),

  // column.accessor("id", {
  //   header: "Aksi",
  //   cell: (cell) =>
  //     h("div", { class: "d-flex gap-2" }, [
  //       h(
  //         "button",
  //         {
  //           class: "btn btn-sm btn-icon btn-primary",
  //           title: "Restore",
  //           onClick: () =>
  //             restoreMitra(`/mitra/${cell.getValue()}/restore`),
  //         },
  //         h("i", { class: "la la-undo fs-2" })
  //       ),
  //       h(
  //         "button",
  //         {
  //           class: "btn btn-sm btn-icon btn-danger",
  //           title: "Hapus Permanen",
  //           onClick: () =>
  //             forceDeleteMitra(`/mitra/${cell.getValue()}/force-delete`),
  //         },
  //         h("i", { class: "la la-trash fs-2" })
  //       ),
  //     ]),
  // }),
];

const refresh = () => {
  paginateRef.value.refetch();
  trashRef.value.refetch();
};

watch(openForm, (val) => {
  if (!val) selected.value = "";
  window.scrollTo(0, 0);
});
</script>

<template>
  <Form
    :selected="selected"
    v-if="openForm"
    @close="openForm = false"
    @refresh="refresh"
  />

  <!-- 🔹 Data Aktif -->
  <div class="card mb-10">
    <div class="card-header align-items-center">
      <h2 class="mb-0">List Mitra</h2>
      <!-- <button
        type="button"
        class="btn btn-sm btn-primary ms-auto"
        v-if="!openForm"
        @click="openForm = true"
      >
        Tambah <i class="la la-plus"></i>
      </button> -->
    </div>
    <div class="card-body">


<paginate
  ref="paginateRef"
  id="table-mitra"
  url="/mitra"
  :columns="columns"
/>

      <!-- <paginate
        ref="paginateRef"
        id="table-mitra"
        url="/mitra"
        :columns="columns"
      /> -->
    </div>
  </div>

  <!-- 🔹 Data Sampah -->
  <!-- <div class="card border-danger">
    <div class="card-header align-items-center">
      <h2 class="mb-0 text-danger">
       Mitra (Trash)
        <small>(data yang telah dihapus)</small>
      </h2>
    </div>
    <div class="card-body">
      <paginate
        ref="trashRef"
        id="table-mitra_trash"
        url="/mitra/trash"
        :columns="trashColumns"
      />
    </div>
  </div> -->
</template>

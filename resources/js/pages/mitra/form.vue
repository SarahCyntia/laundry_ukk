<script setup lang="ts">
import { ref, onMounted, watch } from "vue";
import { toast } from "vue3-toastify";
import axios from "@/libs/axios";
import { jsx } from "vue/jsx-runtime";
import { block, unblock } from "@/libs/utils";
import ApiService from "@/core/services/ApiService";

const props = defineProps({
  selected: {
    type: [Number, String],
    required: true,
  },
});

const emit = defineEmits(["close", "refresh"]);

const loading = ref(false);

const formData = ref({
  nama_laundry: "",
  alamat_laundry: "",
  kecamatan_id: "",
  status_toko: "",
  foto_toko: null,
  jam_buka: "",
  jam_tutup: "",
  user: {
    name: "",
    email: "",
    phone: "",
  },
});
const fileTypes = ref(["image/jpeg", "image/png", "image/jpg"]);
const fotoPreview = ref<string | null>(null);
const foto_toko_path = ref<string | null>(null); // Untuk menyimpan URL dari database
// =====================
// GET DETAIL
// =====================
function getDetail() {
  loading.value = true;

  axios.get(`/mitra/${props.selected}`)
    .then(({ data }) => {
      formData.value = data.data;
    })
    .catch(() => {
      toast.error("Gagal memuat data mitra");
    })
    .finally(() => {
      loading.value = false;
    });
}



function getEdit() {
  block(document.getElementById("form-mitra"));
  ApiService.get("/mitra", props.selected)
    .then(({ data }) => {
      // Pastikan data masuk ke formData agar input teks terisi
      formData.value = data.data; 
      
      // Ambil path foto dari data mitra
      if (data.data.foto_toko) {
        // Sesuaikan dengan letak folder storage kamu
        foto_toko_path.value = "/storage/" + data.data.foto_toko;
      }
    })
    .catch((err: any) => {
      toast.error(err.response?.data?.message || "Gagal memuat data");
    })
    .finally(() => {
      unblock(document.getElementById("form-mitra"));
    });
}

function onFileChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (file) {
    formData.value.foto_toko = file;
    // MEMBUAT PREVIEW: Ini agar saat pilih file baru, gambarnya langsung berubah
    fotoPreview.value = URL.createObjectURL(file);
  }
}
// function getEdit() {
//   block(document.getElementById("form-mitra"));
//   ApiService.get("/mitra", props.selected)
//     .then(({ data }) => {
//       mitra.value = data.mitra;
//       foto_toko.value = data.mitra.foto_toko
//         ? ["/storage/" + data.mitra.foto_toko]
//         : [];
//     })
//     .catch((err: any) => {
//       toast.error(err.response.data.message);
//     })
//     .finally(() => {
//       unblock(document.getElementById("form-mitra"));
//     });
// }

function submit() {
  loading.value = true;

  const payload = new FormData();

  payload.append("nama_laundry", formData.value.nama_laundry);
  payload.append("alamat_laundry", formData.value.alamat_laundry);
  payload.append("kecamatan", String(formData.value.kecamatan_id));
  payload.append("status_toko", formData.value.status_toko);
  payload.append("jam_buka", formData.value.jam_buka);
  payload.append("jam_tutup", formData.value.jam_tutup);

  payload.append("name", formData.value.user.name);
  payload.append("email", formData.value.user.email);
  payload.append("phone", formData.value.user.phone);

  // ⬇️ FOTO
  if (formData.value.foto_toko instanceof File) {
    payload.append("foto_toko", formData.value.foto_toko);
  }

  axios.post(`/mitra/${props.selected}?_method=PUT`, payload, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  })
    .then(() => {
      toast.success("Berhasil update");
      emit("refresh");
      emit("close");
    })
    .catch((err) => {
      console.error(err.response?.data);
      toast.error("Gagal update");
    })
    .finally(() => {
      loading.value = false;
    });
}



// function onFileChange(e: Event) {
//   const file = (e.target as HTMLInputElement).files?.[0];
//   if (file) {
//     formData.value.foto_toko = file;

    
//   }
// }



const kecamatanList = ref([]);

async function getKecamatan() {
  try {
    const { data } = await axios.get("/kecamatan");
    // kecamatanList.value = data;
    kecamatanList.value = data;
    console.log("Kecamatan data:", data);
    console.log("Kecamatan list:", kecamatanList.value);
  } catch (error) {
    console.error("Gagal memuat kecamatan:", error);
  }
}



onMounted(() => {
  getKecamatan();
});

onMounted(async () => {
  if (props.selected) {
    getEdit();
  }
});
watch(
  () => props.selected,
  () => {
    if (props.selected) {
      getEdit();
    }
  }
);
onMounted(() => getDetail());
</script>

<template>
  <!-- <div class="card p-5 mt-20 "> -->
  <div class="card p-5 mt-20 mx-auto" id="form-mitra" style="max-width: 600px; max-height: 60vh; overflow-y: auto;">
    <h3 class="mb-4">Edit Profil Mitra</h3>

    <div v-if="loading" class="text-center">
      <div class="spinner-border"></div>
    </div>



    <div v-else>


      <!-- <div class="mb-3">
        <label class="form-label fw-bold">Foto Laundry</label>
        <input type="file" class="form-control" accept="image/*" @change="onFileChange" />
      </div> -->


      <div class="mb-3">
        <label>Nama Laundry</label>
        <input v-model="formData.nama_laundry" class="form-control" />
      </div>

      <div class="mb-3">
        <label>Nama Pemilik</label>
        <input v-model="formData.user.name" class="form-control" />
      </div>

      <div class="mb-3">
        <label>Email</label>
        <input v-model="formData.user.email" class="form-control" />
      </div>

      <div class="mb-3">
        <label>No HP</label>
        <input v-model="formData.user.phone" class="form-control" />
      </div>

      <div class="mb-3">
        <label>Alamat Laundry</label>
        <textarea v-model="formData.alamat_laundry" class="form-control"></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold">Kecamatan</label>
        <select v-model="formData.kecamatan_id" class="form-control">
          <option value="">-- Pilih Kecamatan --</option>
          <option v-for="kec in kecamatanList" :key="kec.id" :value="kec.id">
            {{ kec.nama }}
          </option>
        </select>
      </div>


      <div class="mb-3">
        <label>Status Toko</label>
        <select v-model="formData.status_toko" class="form-control">
          <option value="buka">BUKA</option>
          <option value="tutup">TUTUP</option>
        </select>
      </div>

      <div class="mb-3">
        <label>Jam Buka</label>
        <input type="time" v-model="formData.jam_buka" class="form-control" />
        <div class="mb-3">
          <label>Jam Tutup</label>
          <input type="time" v-model="formData.jam_tutup" class="form-control" />
        </div>
      </div>



      <div class="col-md-6">
  <div class="fv-row mb-7">
    <label class="form-label fw-bold fs-6">
      <span v-if="fotoPreview || foto_toko_path">Ubah Foto Laundry</span>
      <span v-else>Unggah Foto Laundry</span>

      <div class="mb-3 mt-2">
        <img :src="fotoPreview || foto_toko_path" 
             v-if="fotoPreview || foto_toko_path" 
             class="img-thumbnail"
             style="max-height: 150px;" 
             alt="Preview Foto" />
        <p v-else class="text-muted fs-7">Belum ada foto.</p>
      </div>
    </label>

    <input type="file" class="form-control" accept="image/*" @change="onFileChange" />
    
    <div v-if="fotoPreview || foto_toko_path" class="text-muted fs-8 mt-1">
      *Klik tombol di atas jika ingin mengganti foto saat ini.
    </div>

    <div class="fv-plugins-message-container">
      <div class="fv-help-block">
        <ErrorMessage name="foto_toko" />
      </div>
    </div>
  </div>
</div>
      <!-- <div class="col-md-6">
        <div class="fv-row mb-7">
          <label class="form-label fw-bold fs-6">
            Foto Laundry

            <div class="mb-3">
              <img :src="fotoPreview || foto_toko_path" v-if="fotoPreview || foto_toko_path" class="img-thumbnail"
                style="max-height: 150px;" alt="Preview Foto" />
              <p v-else class="text-muted fs-7">Belum ada foto.</p>
            </div>
          </label>
          <input type="file" class="form-control" accept="image/*" @change="onFileChange" />

          <div class="fv-plugins-message-container">
            <div class="fv-help-block">
              <ErrorMessage name="foto_toko" />
            </div>
          </div>
        </div>
      </div> -->



      <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary" @click="submit">Simpan</button>
        <button class="btn btn-danger" @click="emit('close')">Batal</button>
      </div>
    </div>
  </div>
</template>

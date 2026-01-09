<script setup lang="ts">
import { ref, onMounted } from "vue";
import { toast } from "vue3-toastify";
import axios from "@/libs/axios";
import { jsx } from "vue/jsx-runtime";

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
  jam_buka: "",
  jam_tutup: "",
  user: {
    name: "",
    email: "",
    phone: "",
  },
});

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
  if (formData.value.photo instanceof File) {
    payload.append("foto_toko", formData.value.photo);
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



function onFileChange(e: Event) {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (file) {
    formData.value.photo = file;
  }
}



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

onMounted(() => getDetail());
</script>

<template>
  <div class="card p-5 mt-20">
    <h3 class="mb-4">Edit Profil Mitra</h3>

    <div v-if="loading" class="text-center">
      <div class="spinner-border"></div>
    </div>

    

    <div v-else>

    <div class="mb-3">
  <label class="form-label fw-bold">Foto Laundry</label>
  <input
    type="file"
    class="form-control"
    accept="image/*"
    @change="onFileChange"
  />
</div>


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


        <div class="mt-4 d-flex gap-2">
          <button class="btn btn-primary" @click="submit">Simpan</button>
          <button class="btn btn-danger" @click="emit('close')">Batal</button>
        </div>
      </div>
    </div>
</template>

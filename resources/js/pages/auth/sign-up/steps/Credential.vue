<template>
  <section class="w-100">
    <!-- Pilihan Role -->
    <div class="text-center mb-7">
      <label class="form-label fw-bold text-dark fs-5">Pilih Jenis Akun</label>
      <div class="d-flex justify-content-center gap-3 mt-3">
        <button
          type="button"
          class="btn"
          :class="formData.role === 'pelanggan' ? 'btn-primary' : 'btn-outline-primary'"
          @click="formData.role = 'pelanggan'"
        >
          Pelanggan
        </button>
        <button
          type="button"
          class="btn"
          :class="formData.role === 'mitra' ? 'btn-primary' : 'btn-outline-primary'"
          @click="formData.role = 'mitra'"
        >
          Mitra Laundry
        </button>
      </div>
    </div>

    <hr class="my-5" />

    <!-- Data Akun Umum -->
    <div class="fv-row mb-7">
      <label class="form-label fw-bold text-dark fs-6">Nama</label>
      <Field
        class="form-control form-control-lg form-control-solid"
        type="text"
        name="name"
        autocomplete="off"
        v-model="formData.name"
        placeholder="Masukkan nama lengkap"
      />
      <div class="fv-plugins-message-container">
        <div class="fv-help-block"><ErrorMessage name="name" /></div>
      </div>
    </div>

    <div class="fv-row mb-7">
      <label class="form-label fw-bold text-dark fs-6">Email</label>
      <Field
        class="form-control form-control-lg form-control-solid"
        type="email"
        name="email"
        autocomplete="off"
        v-model="formData.email"
        placeholder="Masukkan email aktif"
      />
      <div class="fv-plugins-message-container">
        <div class="fv-help-block"><ErrorMessage name="email" /></div>
      </div>
    </div>

    <div class="fv-row mb-7">
      <label class="form-label fw-bold text-dark fs-6">No. Telepon</label>
      <Field
        class="form-control form-control-lg form-control-solid"
        type="text"
        name="phone"
        autocomplete="off"
        v-model="formData.phone"
        placeholder="Masukkan nomor telepon aktif"
      />
      <div class="fv-plugins-message-container">
        <div class="fv-help-block"><ErrorMessage name="phone" /></div>
      </div>
    </div>

    <!-- Data Khusus Mitra -->
    <Transition name="fade">
      <div v-if="formData.role === 'mitra'">
        <hr class="my-5" />
        <h4 class="fw-bold text-primary mb-4 text-center">Data Laundry</h4>

        <!-- Nama Laundry -->
        <div class="fv-row mb-7">
          <label class="form-label fw-bold text-dark fs-6">Nama Laundry</label>
          <Field
            class="form-control form-control-lg form-control-solid"
            type="text"
            name="nama_laundry"
            autocomplete="off"
            v-model="formData.nama_laundry"
            placeholder="Masukkan nama laundry"
          />
          <div class="fv-plugins-message-container">
            <div class="fv-help-block"><ErrorMessage name="nama_laundry" /></div>
          </div>
        </div>

        <!-- Alamat Laundry -->
        <div class="fv-row mb-7">
          <label class="form-label fw-bold text-dark fs-6">Alamat Laundry</label>
          <Field
            class="form-control form-control-lg form-control-solid"
            type="text"
            name="alamat_laundry"
            autocomplete="off"
            v-model="formData.alamat_laundry"
            placeholder="Masukkan alamat laundry"
          />
          <div class="fv-plugins-message-container">
            <div class="fv-help-block"><ErrorMessage name="alamat_laundry" /></div>
          </div>
        </div>

        <!-- Foto KTP -->
       <div class="fv-row mb-7">
    <label class="form-label fw-bold text-dark fs-6">Foto KTP</label>

    <!-- Input File -->
    <input
      class="form-control form-control-lg form-control-solid"
      type="file"
      name="foto_ktp"
      accept="image/*"
      @change="handleFileUpload"
    />

    <!-- Info -->
    <small class="text-muted d-block mt-1">
      Format: JPG, PNG, maksimal 2MB
    </small>

    <!-- Preview -->
    <div v-if="previewUrl" class="mt-3">
      <img
        :src="previewUrl"
        alt="Preview KTP"
        class="img-thumbnail"
        style="max-width: 200px; border-radius: 10px;"
      />
    </div>

    <!-- Error Message -->
    <!-- <div v-if="errorMessage" class="text-danger mt-2">
      {{ errorMessage }}
    </div> -->
  </div>

        <!-- Status Toko & Validasi -->
        <!-- <div class="row">
          <div class="col-md-6 mb-7">
            <label class="form-label fw-bold text-dark fs-6">Status Toko</label>
            <select
              class="form-select form-select-lg form-select-solid"
              v-model="formData.status_toko"
            >
              <option value="buka">Buka</option>
              <option value="tutup">Tutup</option>
            </select>
          </div>
          <div class="col-md-6 mb-7">
            <label class="form-label fw-bold text-dark fs-6">Status Validasi</label>
            <select
              class="form-select form-select-lg form-select-solid"
              v-model="formData.status_validasi"
            >
              <option value="menunggu">Menunggu</option>
              <option value="disetujui">Disetujui</option>
              <option value="ditolak">Ditolak</option>
            </select>
          </div>
        </div> -->
      </div>
    </Transition>
  </section>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import { Field, ErrorMessage } from "vee-validate";

export default defineComponent({
  name: "Credential",
  components: { Field, ErrorMessage },
  props: {
    formData: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const previewUrl = ref<string | null>(null);
    const errorMessage = ref<string | null>(null);

    const handleFileUpload = (e: Event) => {
      const target = e.target as HTMLInputElement;
      const file = target.files ? target.files[0] : null;

      if (!file) return;

      const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
      if (!allowedTypes.includes(file.type)) {
        errorMessage.value = "Format file harus JPG atau PNG.";
        props.formData.foto_ktp = null;
        previewUrl.value = null;
        return;
      }

      const maxSize = 2 * 1024 * 1024;
      if (file.size > maxSize) {
        errorMessage.value = "Ukuran file maksimal 2MB.";
        props.formData.foto_ktp = null;
        previewUrl.value = null;
        return;
      }

      props.formData.foto_ktp = file;
      console.log("Uploaded file:", file);
      console.log("Form data foto_ktp:", props.formData.foto_ktp);
      errorMessage.value = null;
      previewUrl.value = URL.createObjectURL(file);
    };

    return { handleFileUpload, previewUrl, errorMessage };
  },
});
</script>


<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>

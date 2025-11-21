<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import ApiService from "@/core/services/ApiService";
import { Field, ErrorMessage, Form as VForm } from "vee-validate";

const props = defineProps({
  selected: {
    type: String,
    default: null,
  },
});

const emit = defineEmits(["close", "refresh"]);

const dataItem = ref({
  pelanggan_id: "",
  mitra_id: "",
  layanan_id: "",
  kurir_id: "",
  berat: 1,
  total_harga: 0,
  alamat_jemput: "",
  alamat_antar: "",
  status: "menunggu-jemput",
});

const pelanggan = ref([]);
const mitra = ref([]);
const layanan = ref([]);
const kurir = ref([]);

const formRef = ref();

// =====================================
// VALIDASI
// =====================================
const formSchema = Yup.object().shape({
  pelanggan_id: Yup.string().required("Pelanggan wajib dipilih"),
  mitra_id: Yup.string().required("Mitra wajib dipilih"),
  layanan_id: Yup.string().required("Layanan wajib dipilih"),
  berat: Yup.number().min(1, "Minimal 1 Kg").required(),
  alamat_jemput: Yup.string().required("Alamat jemput wajib diisi"),
  alamat_antar: Yup.string().required("Alamat antar wajib diisi"),
});

// =====================================
// LOAD DATA DROPDOWN
// =====================================
function loadDropdown() {
  ApiService.get("/pelanggan").then((res) => (pelanggan.value = res.data));
  ApiService.get("/mitra").then((res) => (mitra.value = res.data));
  ApiService.get("/layanan").then((res) => (layanan.value = res.data));
  ApiService.get("/kurir").then((res) => (kurir.value = res.data));
}

// =====================================
// LOAD EDIT
// =====================================
function getEdit() {
  block(document.getElementById("form-transaksi"));
  ApiService.get(`/transaksi/${props.selected}`)
    .then(({ data }) => {
      dataItem.value = data;
    })
    .catch(() => toast.error("Gagal memuat data transaksi"))
    .finally(() => unblock(document.getElementById("form-transaksi")));
}

// =====================================
// HITUNG TOTAL HARGA (BERAT * PER KG)
// =====================================
watch(
  () => [dataItem.value.berat, dataItem.value.layanan_id],
  () => {
    const layananSelected = layanan.value.find(
      (l) => l.id == dataItem.value.layanan_id
    );
    if (layananSelected) {
      dataItem.value.total_harga =
        layananSelected.harga_per_kg * dataItem.value.berat;
    }
  }
);

// =====================================
// SUBMIT DATA
// =====================================
function submit() {
  const fd = new FormData();

  Object.keys(dataItem.value).forEach((key) => {
    fd.append(key, dataItem.value[key]);
  });

  if (props.selected) {
    fd.append("_method", "PUT");
  }

  block(document.getElementById("form-transaksi"));

  axios({
    method: "post",
    url: props.selected
      ? `/transaksi/store/${props.selected}`
      : "/transaksi/store",
    data: fd,
  })
    .then(() => {
      toast.success("Transaksi berhasil disimpan");
      emit("refresh");
      emit("close");
      formRef.value.resetForm();
    })
    .catch((err) => {
      formRef.value.setErrors(err.response?.data?.errors || {});
      toast.error("Gagal menyimpan transaksi");
    })
    .finally(() => {
      unblock(document.getElementById("form-transaksi"));
    });
}

onMounted(() => {
  loadDropdown();
  if (props.selected) getEdit();
});

watch(
  () => props.selected,
  () => {
    if (props.selected) getEdit();
  }
);
</script>

<template>
  <VForm
    class="form card mb-10"
    id="form-transaksi"
    ref="formRef"
    :validation-schema="formSchema"
    @submit="submit"
  >
    <div class="card-header align-items-center">
      <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Transaksi</h2>

      <button
        type="button"
        class="btn btn-sm btn-light-danger ms-auto"
        @click="emit('close')"
      >
        Batal <i class="la la-times-circle p-0"></i>
      </button>
    </div>

    <div class="card-body">
      <div class="row">

        <!-- Pelanggan -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Pelanggan</label>
          <Field
            as="select"
            name="pelanggan_id"
            v-model="dataItem.pelanggan_id"
            class="form-control form-control-lg form-control-solid"
          >
            <option value="">Pilih Pelanggan</option>
            <option v-for="p in pelanggan" :key="p.id" :value="p.id">
              {{ p.nama }}
            </option>
          </Field>
          <ErrorMessage name="pelanggan_id" class="text-danger" />
        </div>

        <!-- Mitra -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Mitra Laundry</label>
          <Field
            as="select"
            name="mitra_id"
            v-model="dataItem.mitra_id"
            class="form-control form-control-lg form-control-solid"
          >
            <option value="">Pilih Mitra</option>
            <option v-for="m in mitra" :key="m.id" :value="m.id">
              {{ m.nama_laundry }}
            </option>
          </Field>
          <ErrorMessage name="mitra_id" class="text-danger" />
        </div>

        <!-- Layanan -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Jenis Layanan</label>
          <Field
            as="select"
            name="layanan_id"
            v-model="dataItem.layanan_id"
            class="form-control form-control-lg form-control-solid"
          >
            <option value="">Pilih Layanan</option>
            <option v-for="l in layanan" :key="l.id" :value="l.id">
              {{ l.nama_layanan }} (Rp {{ l.harga_per_kg }}/kg)
            </option>
          </Field>
          <ErrorMessage name="layanan_id" class="text-danger" />
        </div>

        <!-- Berat -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Berat Cucian (Kg)</label>
          <Field
            type="number"
            name="berat"
            min="1"
            v-model="dataItem.berat"
            class="form-control form-control-lg form-control-solid"
          />
          <ErrorMessage name="berat" class="text-danger" />
        </div>

        <!-- Total Harga -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6">Total Harga</label>
          <input
            class="form-control form-control-lg form-control-solid"
            :value="dataItem.total_harga"
            readonly
          />
        </div>

        <!-- Alamat Jemput -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Alamat Jemput</label>
          <Field
            name="alamat_jemput"
            v-model="dataItem.alamat_jemput"
            class="form-control form-control-lg form-control-solid"
            placeholder="Masukkan alamat penjemputan"
          />
          <ErrorMessage name="alamat_jemput" class="text-danger" />
        </div>

        <!-- Alamat Antar -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Alamat Antar</label>
          <Field
            name="alamat_antar"
            v-model="dataItem.alamat_antar"
            class="form-control form-control-lg form-control-solid"
            placeholder="Masukkan alamat pengantaran"
          />
          <ErrorMessage name="alamat_antar" class="text-danger" />
        </div>

        <!-- Kurir -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6">Kurir (Opsional)</label>
          <Field
            as="select"
            name="kurir_id"
            v-model="dataItem.kurir_id"
            class="form-control form-control-lg form-control-solid"
          >
            <option value="">Belum dipilih</option>
            <option v-for="k in kurir" :key="k.id" :value="k.id">
              {{ k.name }}
            </option>
          </Field>
        </div>

      </div>
    </div>

    <div class="card-footer d-flex">
      <button type="submit" class="btn btn-primary btn-sm ms-auto">
        Simpan
      </button>
    </div>
  </VForm>
</template>

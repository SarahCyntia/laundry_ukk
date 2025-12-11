<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref, watch } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);
const selectedId = ref(null);
const showModal = ref(false);

function openEdit(id) {
    selectedId.value = id;
    showModal.value = true;
}


const statusLanjutan = [
    "diproses",
    "dicuci",
    "dikeringkan",
    "disetrika",
    "siap_diambil",
    "selesai",
];

const jenisLayananList = ref([]);
const jenisLayanan = ref(null);



async function loadJenisLayanan() {
    try {
        const res = await axios.post("/jenis-layanan"); // sesuaikan route kamu
        jenisLayananList.value = res.data.data ?? res.data;
    } catch (e) {
        toast.error("Gagal memuat jenis layanan");
    }
}
onMounted(() => {
    loadJenisLayanan();

    if (props.selected) {
        getEdit();
    }
});

//   berat_estimasi: "",
//   berat_aktual: "",
//   harga_final: "",
//   harga_per_kg: 0,
//   catatan: "",
//   status: "",
//   alasan_penolakan: "",
//   waktu_pelanggan_antar: "",
// });
const formRef = ref();

const order = ref({
    berat_estimasi: "",
    berat_aktual: "",
    harga_final: "",
    harga_per_kg: 0,
    jenis_layanan_id: null,
    catatan: "",
    status: "",
    alasan_penolakan: "",
    waktu_pelanggan_antar: "",
});

// ✅ TARUH TEPAT SETELAH INI
watch(
    () => order.value.berat_aktual,
    (val) => {
        console.log("Berat aktual:", val);
        console.log("Harga per kg:", order.value.harga_per_kg);

        if (val && order.value.harga_per_kg) {
            order.value.harga_final =
                Number(val) * Number(order.value.harga_per_kg);
        } else {
            order.value.harga_final = "";
        }
    }
);




const formSchema = Yup.object().shape({
    berat_estimasi: Yup.string().nullable(),
    berat_aktual: Yup.string().nullable(),
    harga_final: Yup.string().nullable(),
    catatan: Yup.string().nullable(),
    status: Yup.string().required("Status wajib diisi"),
    alasan_penolakan: Yup.string().nullable(),
});


function getHargaLayanan(id) {
    console.log("Ambil harga layanan ID:", id);

    return axios.get(`/jenis-layanan/${id}`)
        .then(res => {
            console.log("Response harga layanan:", res.data);
            order.value.harga_per_kg = res.data.harga_per_kg; // sesuaikan kalau beda struktur
        })
        .catch(err => {
            console.log("ERROR HARGA:", err);
            order.value.harga_per_kg = 0;
        });
}


const hargaLayananMap = {};



function getEdit() {
    block(document.getElementById("form-order"));

    axios.get(`/order/${props.selected}`)
        .then(async ({ data }) => {

            const d = data.order;
            const layanan = data.jenis_layanan;
            jenisLayanan.value = layanan; // ⬅ Wajib supaya muncul di form


            order.value = {
                berat_estimasi: d.berat_estimasi ?? "",
                berat_aktual: d.berat_aktual ?? "",
                harga_final: d.harga_final ?? "",
                harga_per_kg: 0,
                jenis_layanan_id: layanan?.id || null,
                catatan: d.catatan ?? "",
                status: d.status ?? "",
                alasan_penolakan: d.alasan_penolakan ?? "",
                waktu_pelanggan_antar: d.waktu_pelanggan_antar ?? "",
            };

            // ✅ AMBIL HARGA LANGSUNG DARI DATABASE
            if (layanan && layanan.id) {
                await getHargaLayanan(layanan.id);
            }
            // if (layanan?.id) {
            //     await getHargaLayanan(layanan.id);
            // }

        })
        .catch(err => {
            console.log("ERROR GET:", err);
            toast.error("Gagal mengambil data order");
        })
        .finally(() => {
            unblock(document.getElementById("form-order"));
        });
}




const statusSteps = [
    "menunggu_konfirmasi_mitra",
    "diterima",
    "diproses",
    "dicuci",
    "dikeringkan",
    "disetrika",
    "siap_diambil",
    "selesai",
    "ditolak"
];
function isDisabledStatus(status: string) {
    const currentIndex = statusSteps.indexOf(order.value.status);
    const optionIndex = statusSteps.indexOf(status);

    // Kalau status tidak ditemukan, jangan dikunci
    if (currentIndex === -1 || optionIndex === -1) return false;

    // ✅ KUNCI status yang posisinya SEBELUM status saat ini
    return optionIndex < currentIndex;
}


// =======================
// SUBMIT (UPDATE ORDER)
// =======================
function submit() {
    block(document.getElementById("form-order"));

    axios({
        method: "post",
        url: `/order/${props.selected}`,
        data: { ...order.value, _method: "PUT" },
    })
        .then(() => {
            toast.success("Order berhasil diperbarui");
            emit("refresh");
            emit("close");
        })
        .catch(err => {
            toast.error(err.response.data.message || "Gagal menyimpan");
            formRef.value.setErrors(err.response.data.errors);
        })
        .finally(() => {
            unblock(document.getElementById("form-order"));
        });
}

// AMBIL DATA SAAT EDIT
// onMounted(() => {
//     if (props.selected) getEdit();
// });

onMounted(() => {
    if (props.selected) {
        getEdit();
    }
});

// ✅ Pantau perubahan selected (Edit Mode)
watch(
    () => props.selected,
    () => {
        if (props.selected) {
            getEdit();
        }
    }
);


</script>


<template>
    <VForm class="form card mb-10" @submit="submit" :validation-schema="formSchema" ref="formRef" id="form-order">
        <OrderForm v-if="showModal" :selected="selectedId" @close="showModal = false" @refresh="loadOrder" />

        <div class="card-header">
            <h2>{{ selected ? "Edit Order" : "Tambah Order" }}</h2>
            <!-- <button class="btn btn-sm btn-primary" @click="openEdit(order.id)">
                Edit
            </button> -->

            <button class="btn btn-light-danger btn-sm ms-auto" @click="emit('close')" type="button">
                Batal
            </button>
        </div>

        <div class="card-body row g-5">

            <div class="col-md-6">
                <label class="form-label fw-bold">Jenis Layanan</label>
                <input type="text" class="form-control" :value="jenisLayanan?.nama_layanan" disabled />
            </div>



            <div class="col-md-6">
                <label class="form-label fw-bold">Berat Estimasi (kg)</label>
                <Field class="form-control" name="berat_estimasi" v-model="order.berat_estimasi" />
                <ErrorMessage name="berat_estimasi" class="text-danger" />
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Berat Aktual (kg)</label>
                <Field class="form-control" name="berat_aktual" v-model="order.berat_aktual" />
                <ErrorMessage name="berat_aktual" class="text-danger" />
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Harga Final</label>
                <!-- <Field class="form-control" name="harga_final" v-model="order.harga_final" /> -->
                <Field name="harga_final" v-model="order.harga_final" readonly class="form-control" />
                <ErrorMessage name="harga_final" class="text-danger" />
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Status</label>

                <Field as="select" class="form-select" name="status" v-model="order.status">

                    <option value="">-- Pilih Status --</option>

                    <!-- Status dari database (tetap muncul & terkunci) -->
                    <option v-if="order.status" :value="order.status" disabled>
                        {{ order.status.replaceAll('_', ' ') }}
                    </option>

                    <!-- Semua status lanjutan -->
                    <option v-for="s in statusLanjutan" :key="s" :value="s" :disabled="isDisabledStatus(s)">
                        {{ s.replaceAll('_', ' ') }}
                    </option>
                </Field>

                <ErrorMessage name="status" class="text-danger" />
            </div>


            <!-- <div class="col-md-6">
                <label class="form-label fw-bold">Status</label> -->
            <!-- <Field as="select" class="form-select" name="status" v-model="order.status"> -->
            <!-- <Field as="select" class="form-select" name="status" v-model="order.status"
                    :disabled="order.status === 'menunggu_konfirmasi_mitra'">

                    <option value="">-- Pilih Status --</option>

                    <option v-for="s in statusLanjutan" :key="s" :value="s">
                        {{ s.replace('_', ' ') }}
                    </option>
                </Field> -->
            <!-- <Field as="select" class="form-select" name="status" v-model="order.status">

                    <option value="">-- Pilih Status --</option>
                    <option v-if="order.status" :value="order.status">
                        {{ order.status.replaceAll('_', ' ') }}
                    </option>

                    <option v-for="s in statusLanjutan" :key="s" :value="s">
                        {{ s.replaceAll('_', ' ') }}
                    </option>
                </Field>
                <ErrorMessage name="status" class="text-danger" />
            </div> -->

            <div class="col-12">
                <label class="form-label fw-bold">Catatan</label>
                <Field as="textarea" class="form-control" name="catatan" v-model="order.catatan" />
                <ErrorMessage name="catatan" class="text-danger" />
            </div>

            <div class="col-12" v-if="order.status === 'ditolak'">
                <label class="form-label fw-bold">Alasan Penolakan</label>
                <Field class="form-control" name="alasan_penolakan" v-model="order.alasan_penolakan" />
                <ErrorMessage name="alasan_penolakan" class="text-danger" />
            </div>

        </div>

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-primary ms-auto">Simpan</button>
        </div>
    </VForm>
</template>
